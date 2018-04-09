<?php
namespace Lelesys\Plugin\Newsletter\Domain\Service;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Domain\Model\Newsletter;
use TYPO3\TYPO3CR\Domain\Model\NodeTemplate;
use TYPO3\TYPO3CR\Domain\Model\PersistentNodeInterface;
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\TYPO3CR\Domain\Model\Node;
use Neos\Flow\Http\Request as Request;

/**
 * Newsletter Service
 *
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
	 * @var \Lelesys\Plugin\Newsletter\Service\EmailNotificationService
	 */
	protected $emailNotificationService;

	/**
	 * EmailLog Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailLogService
	 */
	protected $emailLogService;

	/**
	 * NodeData Repository
	 *
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository
	 */
	protected $nodeDataRepository;

	/**
	 * Person Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\PersonService
	 */
	protected $personService;

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Service\NewsletterBuildService
	 */
	protected $newsletterBuildService;

	/**
	 * ResourceManager
	 *
	 * @Flow\Inject
	 * @var \Neos\Flow\ResourceManagement\ResourceManager
	 */
	protected $resourceManager;

	/**
	 * Inject PersistenceManagerInterface
	 *
	 * @Flow\Inject
	 * @var \Neos\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Injection for property mapper
	 *
	 * @Flow\Inject
	 * @var \Neos\Flow\Property\PropertyMapper
	 */
	protected $propertyMapper;

	/**
	 * Asset repository
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Media\Domain\Repository\AssetRepository
	 */
	protected $assetRepository;

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Contextfactory
	 *
	 * @Flow\Inject
	 * @var TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface
	 */
	protected $contextFactory;

	/**
	 * Inject ConfigurationManager
	 *
	 * @Flow\Inject
	 * @var \Neos\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Inject RouterInterface
	 *
	 * @Flow\Inject
	 * @var \Neos\Flow\Mvc\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * Inject dispatcher
	 *
	 * @Flow\Inject
	 * @var \Neos\Flow\Mvc\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * The security conntext
	 *
	 * @var \Neos\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * Inject ObjectManagerInterface
	 *
	 * @var \Neos\Flow\ObjectManagement\ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

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
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @return array
	 */
	public function getAllRecipientsByGroups(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$receipientsByGroupParty = array();
		$receipientsByGroupStatic = array();
		foreach ($newsletter->getRecipientGroups() as $group) {
			if ($group instanceof \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party) {
				$receipientsByGroupParty[] = array($group->getTitle(), $group->getRecipients());
			} else {
				$recipientList = array();
				$staticList = \Neos\Utility\Arrays::trimExplode(',', $group->getRecipients());
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
	 * Sends test email
	 *
	 * @param string $adminEmail Admin email
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function sendTestEmail($adminEmail, \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$subject = $newsletter->getSubject();
		$fromName = $this->settings['email']['senderName'];
		$message = $this->newsletterBuildService->buildMailContents($newsletter, 'html');
		$attachments = array();
		$newsletterAttachments = $newsletter->getAttachments();
		foreach($newsletterAttachments as $newsletterAttachment) {
			$attachments[$this->resourceManager->getPersistentResourcesStorageBaseUri() . $newsletterAttachment->getResource()->getResourcePointer()->getHash()] = $newsletterAttachment->getTitle();
		}
		$messageBody = $this->emailNotificationService->buildEmailMessage(array('mailContent' => $message), 'html');
		$this->emailNotificationService->sendMail($subject, $messageBody, $adminEmail, $fromName, $attachments);
	}

	/**
	 * Sends email
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function sendEmail(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$allArrays['list'] = $allArrays['personEmailList'] = array();
		$list = $staticLists = array();
		// send to all subscribed users and ignore the categories or recipient group selection for this newsletter
		if ($newsletter->getSendToAll()) {
			$allArrays = $this->sendNewsletterEmailToRecipients($newsletter, $this->personService->listAllApproved());
		} else {
			$categoryRecipients = $this->personService->findByRecipientsByCategories($newsletter);
			if($categoryRecipients) {
				$allArrays = $this->sendNewsletterEmailToRecipients($newsletter, $categoryRecipients);
			}

			$allArrays = $this->sendNewsletterEmailToRecipients($newsletter, $newsletter->getRecipients(), $allArrays['list'], $allArrays['personEmailList']);
			foreach ($newsletter->getRecipientGroups() as $group) {
				if ($group instanceof \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party) {
					$recipeints = $group->getRecipients();
					$personListArray = $this->sendNewsletterEmailToRecipients($newsletter, $recipeints, $allArrays['list'], $allArrays['personEmailList']);
					$allArrays['list'] = $personListArray['list'];
					$allArrays['personEmailList'] = $personListArray['personEmailList'];
				} else {
					$staticLists = array_merge($staticLists, \Neos\Utility\Arrays::trimExplode(',', $group->getRecipients()));
				}
			}
		}

		if ((empty($allArrays['personEmailList']) === FALSE
				&& empty($allArrays['list']) === FALSE)
				|| (empty($allArrays['list']) === FALSE)) {
			$groupEmails = array_unique($allArrays['personEmailList']);
			$staticEmails = array_unique($staticLists);
			$finalList = array_merge($groupEmails, $staticEmails);
			$list = array_unique($finalList);
			foreach ($list as $recepeintIdentifier => $email) {
				if (is_int($recepeintIdentifier) === TRUE) {
					$this->emailLogService->create($newsletter, \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog::RECIPIENT_TYPE_STATIC, $email);
				} else {
					$this->emailLogService->create($newsletter, \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog::RECIPIENT_TYPE_PERSON, $recepeintIdentifier);
				}
			}
		} elseif (count($staticLists) > 0) {
			$staticEmails = array_unique($staticLists);
			foreach ($staticEmails as $email) {
				$this->emailLogService->create($newsletter, \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog::RECIPIENT_TYPE_STATIC, $email);
			}
		}
	}

	/**
	 * Send Newsletter Email To Recipients
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @param array $recipients Array of recipients
	 * @param array $list List
	 * @param array $personEmailList Array of email list
	 */
	public function sendNewsletterEmailToRecipients(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $recipients, $list = array(), $personEmailList = array()) {
		foreach ($recipients as $recipient) {
			if ((($this->personService->isUserApproved($recipient) === TRUE)
					&& $recipient->isSubscribedToNewsletter() === TRUE)
					|| (($this->personService->isUserApproved($recipient) === TRUE)
					&& (count($recipient->getNewsletterCategories()) === 0)
					&& $recipient->isSubscribedToNewsletter() === TRUE)) {
				$list[] = $recipient->getUuid();
				$personEmailList[$recipient->getUuid()] = $recipient->getPrimaryElectronicAddress()->getIdentifier();
			}
		}
		return array('list' => $list, 'personEmailList' => $personEmailList);
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
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category object
	 * @return array
	 */
	public function getAllNewslettersByCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		return $this->newsletterRepository->getNewslettersByCategory($category);
	}

	/**
	 * Checks if newsletter category and recipients category matches
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $recipient Recipient object
	 * @return boolean
	 */
	public function isValidCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $recipient) {
		foreach ($newsletter->getCategories() as $newsletterCategory) {
			if (in_array($newsletterCategory, $recipient->getNewsletterCategories()->toArray())) {
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
	 * Adds news newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter Newsletter object
	 * @param array $attachments Attachments
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter, $attachments = array()) {
		$this->newsletterRepository->add($this->addAttachments($newNewsletter, $attachments));
	}

	/**
	 * Adds attachment to newsletter during create and edit actions
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter Newsletter object
	 * @param type $attachments  Attachments
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter Newsletter
	 */
	public function addAttachments(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter, $attachments = array()) {
		if (empty($attachments) === FALSE) {
			foreach ($attachments as $attachment) {
				if (!empty($attachment['name'])) {
					$resource = $this->propertyMapper->convert($attachment, 'Neos\Flow\ResourceManagement\PersistentResource');
					$file = new \TYPO3\Media\Domain\Model\Document($resource);
					$file->setTitle($attachment['name']);
					$this->assetRepository->add($file);
					$newNewsletter->addAttachment($file);
				}
			}
		}
		return $newNewsletter;
	}

	/**
	 * Removed contentNode form newsletter which are deleted
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
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
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @param array $attachments Attachments
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $attachments = array()) {
		$this->newsletterRepository->update($this->addAttachments($newsletter, $attachments));
	}

	/**
	 * Deletes newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$newsletterEmailLogs = $this->emailLogService->getAllEmailLogsByNewsletter($newsletter);
		foreach ($newsletterEmailLogs as $emailLog) {
			$this->emailLogService->delete($emailLog);
		}
		$attachments = $newsletter->getAttachments();
		foreach ($attachments as $attachment) {
			$newsletter->removeAttachment($attachment);
			$this->update($newsletter);
			$this->assetRepository->remove($attachment);
			$this->persistenceManager->persistAll();
		}
		$this->newsletterRepository->remove($newsletter);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Deletes Related Recipients
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function deleteRelatedRecipients(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$newsletters = $this->newsletterRepository->getNewslettersByRecipient($person);
		foreach ($newsletters as $newsletter) {
			$newsletter->removeRecipient($person);
			$this->newsletterRepository->update($newsletter);
		}
	}

	/**
	 * Delete Related Recipient Group Party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
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
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList Static list
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
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return void
	 */
	public function deleteRelatedCategories(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$newsletters = $this->newsletterRepository->getNewslettersByCategory($category);
		foreach ($newsletters as $newsletter) {
			$newsletter->removeCategories($category);
			$this->newsletterRepository->update($newsletter);
		}
	}

	/**
	 * Gets all content node
	 *
	 * @param string $contentNode Node identifier
	 * @return \TYPO3\TYPO3CR\Domain\Model\NodeData $newsletterNode
	 */
	public function getContentNode($contentNode = NULL) {
		if ($contentNode !== NULL) {
			$newsletterNode = $this->nodeDataRepository->findByIdentifier($contentNode);
			return $newsletterNode;
		}
	}

	/**
	 * Delete attachments of the newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @param \TYPO3\Media\Domain\Model\Document $attachment Attachment object
	 * @return void
	 */
	public function deleteAttachment(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, \TYPO3\Media\Domain\Model\Document $attachment) {
		$newsletter->removeAttachment($attachment);
		$this->update($newsletter);
		$this->assetRepository->remove($attachment);
		$this->persistenceManager->persistAll();
	}
}
?>