<?php

namespace Lelesys\Plugin\Newsletter\Domain\Service;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Domain\Model\Newsletter;
use TYPO3\TYPO3CR\Domain\Model\NodeTemplate;
use TYPO3\TYPO3CR\Domain\Model\PersistentNodeInterface;

/**
 * @Flow\Scope("singleton")
 */
class NewsletterService {

	/**
	 * Newsletter Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\NewsletterRepository
	 */
	protected $newsletterRepository;

	/**
	 * Email Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailNotificationService
	 */
	protected $emailNotificationService;

	/**
	 * NodeData Repository
	 *
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository
	 */
	protected $nodeDataRepository;

	/**
	 * NodeTypeManager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * Person Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\PersonService
	 */
	protected $personService;

	/**
	 * ResourceManager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\ResourceManager
	 */
	protected $resourceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Injects settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Checks for deleted nodes from newsletter
	 *
	 * @return void
	 */
	public function checkDeletedNodeFromNewsletter() {
		$newsletters = $this->listAll();
		foreach ($newsletters as $newsletter) {
			if ($newsletter->getContentNode() !== NULL) {
				$node = $this->getContentNode($newsletter->getContentNode());
				if ($node === NULL) {
					$this->removeContentNode($newsletter);
				}
			}
		}
	}

	/**
	 * List of all Recipients By Groups
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @return void
	 */
	public function getAllRecipientsByGroups(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$receipientsByGroupParty = array();
		$receipientsByGroupStatic = array();
		foreach ($newsletter->getRecipientGroups() as $group) {
			if ($group instanceof \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party) {
				$receipientsByGroupParty[] = array($group->getTitle(), $group->getRecipients());
			} else {
				$staticList = explode(',', $group->getRecipients());
				foreach ($staticList as $recipient) {
					$recipientList[] = $recipient;
				}
				if (count($staticList) > 0) {
					$receipientsByGroupStatic[] = array($group->getTitle(), $recipientList);
				}
			}
		}
		return array(
			'GroupParty' => $receipientsByGroupParty,
			'GroupStatic' => $receipientsByGroupStatic
		);
	}

	/**
	 * Sends email
	 * @param string $adminEmail
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function sendTestEmail($adminEmail, \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$subject = $newsletter->getSubject();
		$fromName = $this->settings['email']['senderName'];
		$childNodes = array();
		$node = $this->getContentNode($newsletter->getContentNode());
		foreach ($node->getChildNodes() as $value) {
			foreach ($value->getChildNodes() as $child) {
				$childNodes[] = $child;
			}
		}
		$attachments = array();
		if ($newsletter->getAttachments() !== NULL) {
			$attachments['path'] = $this->resourceManager->getPersistentResourcesStorageBaseUri() . $newsletter->getAttachments()->getResourcePointer()->getHash();
			$attachments['name'] = $newsletter->getAttachments()->getFilename();
		}
		$message = $this->emailNotificationService->buildEmailMessage('Newsletter.html', array('contentNode' => $childNodes));
		$this->emailNotificationService->sendMail($subject, $message, $adminEmail, $fromName, $attachments);
	}

	/**
	 * Sends email
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function sendEmail(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$childNodes = array();
		$node = $this->getContentNode($newsletter->getContentNode());
		foreach ($node->getChildNodes() as $value) {
			foreach ($value->getChildNodes() as $child) {
				$childNodes[] = $child;
			}
		}
		$this->sendNewsletterEmailToRecipients($newsletter, $childNodes, $newsletter->getRecipients());
		foreach ($newsletter->getRecipientGroups() as $group) {
			if ($group instanceof \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party) {
				$this->sendNewsletterEmailToRecipients($newsletter, $childNodes, $group->getRecipients());
			} else {
				$staticList = explode(',', $group->getRecipients());
				if (count($staticList) > 0) {
					$this->sendNewsletterEmailToRecipients($newsletter, $childNodes, $staticList);
				}
			}
		}
	}

	/**
	 * Send Newsletter Email To Recipients
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @param array $childNodes
	 * @param array $recipients
	 */
	public function sendNewsletterEmailToRecipients(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $childNodes, $recipients) {
		$recipientAddress = array();
		$contentType = array();
		foreach ($recipients as $recipient) {
			if (is_object($recipient) === TRUE) {
				if ((($this->personService->isUserApproved($recipient) === TRUE) &&
						($this->isValidCategory($newsletter, $recipient) === TRUE)) ||
						(($this->personService->isUserApproved($recipient) === TRUE) &&
						(count($recipient->getCategories()) === 0))) {
					if ($recipient->getAcceptsHtml() === TRUE) {
						$code = sha1($recipient->getPrimaryElectronicAddress()->getIdentifier() . $recipient->getUuid());
						$contentType['html'] = 'text/html';
						$message = $this->emailNotificationService->buildEmailMessage('Newsletter.html', array('contentNode' => $childNodes, 'recipientId' => $recipient->getUuid(), 'code' => $code));
						$recipientAddress['html'][] = array($recipient->getPrimaryElectronicAddress()->getIdentifier(), $message, $recipient->getName()->getFirstName(),);
					} else {
						$code = sha1($recipient->getPrimaryElectronicAddress()->getIdentifier() . $recipient->getUuid());
						$contentType['text'] = 'text/plain';
						$message = $this->emailNotificationService->buildEmailMessage('Newsletter.txt', array('contentNode' => $childNodes, 'recipientId' => $recipient->getUuid(), 'code' => $code));
						$recipientAddress['text'][] = array($recipient->getPrimaryElectronicAddress()->getIdentifier(), $message, $recipient->getName()->getFirstName());
					}
				}
			} else {
				$contentType['text'] = 'text/plain';
				$message = $this->emailNotificationService->buildEmailMessage('Newsletter.txt', array('contentNode' => $childNodes, 'recipient' => $recipient));
				$recipientAddress['text'][] = array(trim($recipient), $message);
			}
		}
		$fromEmail = $newsletter->getFromEmail();
		$fromName = $newsletter->getFromName();
		$subject = $newsletter->getSubject();
		$priority = $newsletter->getPriority();
		$characterSet = $newsletter->getCharacterSet();
		$attachments = array();
		if ($newsletter->getAttachments() !== NULL) {
			$attachments['path'] = $this->resourceManager->getPersistentResourcesStorageBaseUri() . $newsletter->getAttachments()->getResourcePointer()->getHash();
			$attachments['name'] = $newsletter->getAttachments()->getFilename();
		}
		$replyEmail = $newsletter->getReplyToEmail();
		$replyName = $newsletter->getReplyToName();
		if (isset($contentType['html'])) {
			$this->emailNotificationService->sendNewsletterMail($fromEmail, $fromName, $replyEmail, $replyName, $subject, $priority, $characterSet, $attachments, $contentType['html'], $recipientAddress['html']);
		}
		if (isset($contentType['text'])) {
			$this->emailNotificationService->sendNewsletterMail($fromEmail, $fromName, $replyEmail, $replyName, $subject, $priority, $characterSet, $attachments, $contentType['text'], $recipientAddress['text']);
		}
	}

	/**
	 * List of all newsletters
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter Newsletters
	 */
	public function listAll() {
		return $this->newsletterRepository->findAll();
	}

	/**
	 * Get all newsletters by category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category
	 * @return array
	 */
	public function getAllNewslettersByCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		return $this->newsletterRepository->getNewslettersByCategory($category);
	}

	/**
	 * Checks if newsletter category and recipients category matches
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $recipient
	 * @return boolean
	 */
	public function isValidCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $recipient) {
		foreach ($newsletter->getCategories() as $newsletterCategory) {
			if (in_array($newsletterCategory, $recipient->getCategories()->toArray())) {
				return TRUE;
			}
		}
	}

	/**
	 * Gets all newsletter childnodes
	 *
	 * @return \TYPO3\TYPO3CR\Domain\Model\NodeData $nodes
	 */
	public function getNewsletterChildNodes() {
		$nodes = $this->nodeDataRepository->findByNodeType('Lelesys.Plugin.Newsletter:Newsletter')->toArray();
		$newsletterNodes = $this->listAll();
		foreach ($newsletterNodes as $newsletterNode) {
			if (($key = array_search($newsletterNode->getContentNode(), $nodes)) !== FALSE) {
				unset($nodes[$key]);
			}
		}
		return $nodes;
	}

	/**
	 * Get all newsletter childnodes
	 *
	 * @return \TYPO3\TYPO3CR\Domain\Model\NodeData $nodes
	 */
	public function getAllNewsletterChildNodes() {
		$nodes = $this->nodeDataRepository->findByNodeType('Lelesys.Plugin.Newsletter:Newsletter')->toArray();
		return $nodes;
	}

	/**
	 * Gets all content node
	 *
	 * @param string $contentNode
	 * @return \TYPO3\TYPO3CR\Domain\Model\NodeData $newsletterNode
	 */
	public function getContentNode($contentNode = NULL) {
		if ($contentNode !== NULL) {
			$newsletterNode = $this->nodeDataRepository->findByIdentifier($contentNode);
			return $newsletterNode;
		}
	}

	/**
	 * Adds news newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter) {
		$this->newsletterRepository->add($newNewsletter);
	}

	/**
	 * Removed contentNode form newsletter which are deleted
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @return void
	 */
	public function removeContentNode(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$newsletter->setContentNode(NULL);
		$this->update($newsletter);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Updates newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$this->newsletterRepository->update($newsletter);
	}

	/**
	 * Deletes newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$this->newsletterRepository->remove($newsletter);
	}

	/**
	 * Deletes Related Recipients
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person
	 * @return void
	 */
	public function deleteRelatedRecipients(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$newsletters = $this->newsletterRepository->getNewslettersByRecipient($person);
		foreach ($newsletters as $newsletter) {
			$newsletter->removeRecipients($person);
			$this->newsletterRepository->update($newsletter);
		}
	}

	/**
	 * Delete Related Recipient Group Party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party
	 * @return void
	 */
	public function deleteRelatedRecipientGroupParty(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		$newsletters = $this->newsletterRepository->getNewslettersByRecipientGroup($party);
		foreach ($newsletters as $newsletter) {
			$newsletter->removeRecipientGroups($party);
			$this->newsletterRepository->update($newsletter);
		}
	}

	/**
	 * Delete Related Recipient Group StaticList
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function deleteRelatedRecipientGroupStaticList(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		$newsletters = $this->newsletterRepository->getNewslettersByRecipientGroup($staticList);
		foreach ($newsletters as $newsletter) {
			$newsletter->removeRecipientGroups($staticList);
			$this->newsletterRepository->update($newsletter);
		}
	}

	/**
	 * Delete Related Categories
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category
	 * @return void
	 */
	public function deleteRelatedCategories(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$newsletters = $this->newsletterRepository->getNewslettersByCategory($category);
		foreach ($newsletters as $newsletter) {
			$newsletter->removeCategories($category);
			$this->newsletterRepository->update($newsletter);
		}
	}

}

?>