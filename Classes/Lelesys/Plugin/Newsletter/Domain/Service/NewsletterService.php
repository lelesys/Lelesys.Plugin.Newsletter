<?php
namespace Lelesys\Plugin\Newsletter\Domain\Service;

/*
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
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TYPO3\Flow\Http\Request as Request;

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
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailNotificationService
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
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterBuildService
	 */
	protected $newsletterBuildService;

	/**
	 * ResourceManager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\ResourceManager
	 */
	protected $resourceManager;

	/**
	 * Inject PersistenceManagerInterface
	 *
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
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Inject RouterInterface
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * Inject dispatcher
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * The security conntext
	 *
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * Inject ObjectManagerInterface
	 *
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
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
	 * Sends test email
	 *
	 * @param string $adminEmail Admin email
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function sendTestEmail($adminEmail, \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$subject = $newsletter->getSubject();
		$fromName = $this->settings['email']['senderName'];
		$message = $this->newsletterBuildService->buildMailContents('Newsletter.html', array('recipient' => $adminEmail), $newsletter, array('html' => 'text/html'));
		$attachments = array();
		if ($newsletter->getAttachments() !== NULL) {
			$attachments['path'] = $this->resourceManager->getPersistentResourcesStorageBaseUri() . $newsletter->getAttachments()->getResourcePointer()->getHash();
			$attachments['name'] = $newsletter->getAttachments()->getFilename();
		}
		$this->emailNotificationService->sendMail($subject, $message, $adminEmail, $fromName, $attachments);
	}

	/**
	 * Sends email
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function sendEmail(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$allArrays = $this->sendNewsletterEmailToRecipients($newsletter, $newsletter->getRecipients());
		$personEmailList = $list = $staticLists = array();
		foreach ($newsletter->getRecipientGroups() as $group) {
			if ($group instanceof \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party) {
				$recipeints = $group->getRecipients();
				$personListArray = $this->sendNewsletterEmailToRecipients($newsletter, $recipeints, $allArrays['list'], $allArrays['personEmailList']);
				$list = $personListArray['list'];
				$personEmailList = $personListArray['personEmailList'];
			} else {
				$staticLists = \TYPO3\Flow\Utility\Arrays::trimExplode(',', $group->getRecipients());
			}
		}
		if ((count($personEmailList) > 0 && count($list) > 0) || ($allArrays['list'] > 0)) {
			if (empty($personEmailList) === TRUE) {
				$groupEmails = array_unique($allArrays['personEmailList']);
			} else {
				$groupEmails = array_unique($personEmailList);
			}
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
		$recipeintType = '';
		foreach ($recipients as $recipient) {
			if ((($this->personService->isUserApproved($recipient) === TRUE) &&
					($this->isValidCategory($newsletter, $recipient) === TRUE)) ||
					(($this->personService->isUserApproved($recipient) === TRUE) &&
					(count($recipient->getCategories()) === 0))) {
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
	 * Adds news newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter Newsletter object
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter) {
		$this->newsletterRepository->add($newNewsletter);
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
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$this->newsletterRepository->update($newsletter);
	}

	/**
	 * Deletes newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
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
			$newsletter->removeRecipients($person);
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

}
?>