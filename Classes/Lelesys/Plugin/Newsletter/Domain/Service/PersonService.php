<?php
namespace Lelesys\Plugin\Newsletter\Domain\Service;

/*                                                                         *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person;
use \TYPO3\Fluid\View\StandaloneView;

/**
 * The Person Service
 *
 * @Flow\Scope("singleton")
 */
class PersonService {

	/**
	 * Person Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\PersonRepository
	 */
	protected $personRepository;

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * EmailNotification Service
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
	 * Bootstrap
	 *
	 * @var \TYPO3\Flow\Core\Bootstrap
	 * @Flow\Inject
	 */
	protected $bootstrap;

	/**
	 * Inject email service
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\Doctrine\PersistenceManager
	 */
	protected $persistenceManager;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * gets all recipients
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person
	 */
	public function listAll() {
		return $this->personRepository->findAll();
	}

	/**
	 * Gets all approved recipients
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person
	 */
	public function listAllApproved() {
		return $this->personRepository->findAllApprovedUsers();
	}

	/**
	 * Unsubscribe newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $recipient To unsubcribe
	 * @param string $code Code to check the link validity
	 * @return void
	 */
	public function unSubscribe($recipient, $code) {
		if ($recipient !== NULL && $code !== NULL) {
			$user = $this->getUserFromIdentifier($recipient);
			if ($user !== NULL) {
				$newcode = sha1($user->getPrimaryElectronicAddress()->getIdentifier() . $user->getUuid());
				$approved = $user->getPrimaryElectronicAddress()->isApproved();
				if (($approved === TRUE) && ($code === $newcode)) {
					$this->emailLogService->updateRecipient($user);
					$this->delete($user);
					return 1;
				} elseif ($code !== $newcode || $approved === FALSE) {
						// Link not valid
					return 0;
				}
			}
		}
	}

	/**
	 * Checks if existing user
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson Person object
	 * @return boolean
	 */
	public function isExistingUser(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson) {
		$existingUser = $this->personRepository->isExistingUser($newPerson);
		if (!empty($existingUser)) {
			return 1;
		}
	}

	/**
	 * Checks if user is approved
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return boolean
	 */
	public function isUserApproved(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		if ($person->getPrimaryElectronicAddress()->isApproved() === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Adds new recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson Person object
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson) {
		$this->personRepository->add($newPerson);
			// To check if the user is subcribed user
		$code = sha1($newPerson->getPrimaryElectronicAddress()->getIdentifier() . $newPerson->getUuid());
		$baseUrl = $this->bootstrap->getActiveRequestHandler()->getHttpRequest()->getBaseUri();
		$values = array('url' => $baseUrl, 'code' => $code, 'user' => $newPerson);

		$recipientAddress = $newPerson->getPrimaryElectronicAddress()->getIdentifier();
		$recipientName = $newPerson->getName();
		$subject = $this->settings['email']['subject'];
		$message = $this->emailNotificationService->buildEmailMessage('SubscribedNotification.html', $values);
		$this->emailNotificationService->sendMail($subject, $message, $recipientAddress, $recipientName);
	}

	/**
	 * User confirmation after subscription
	 *
	 * @param string $code code to verify user
	 * @param string $userIdentifier userid
	 * @return interger
	 */
	public function confirmSubscription($code, $userIdentifier) {
		if ($userIdentifier !== NULL && $code !== NULL) {
			$user = $this->getUserFromIdentifier($userIdentifier);
			if ($user !== NULL) {
				$newcode = sha1($user->getPrimaryElectronicAddress()->getIdentifier() . $user->getUuid());
				if (($user->getPrimaryElectronicAddress()->isApproved() === FALSE) && ($code === $newcode)) {
					$this->emailApprovalByUser($user);
					return 1;
				} elseif ($code !== $newcode) {
						// Link not valid
					return 0;
				} elseif (($user->getPrimaryElectronicAddress()->isApproved() === TRUE) && ($code === $newcode)) {
						// already confirmed user
					return 2;
				}
			}
		}
	}

	/**
	 * Get the user object from given identifier
	 *
	 * @param string $userIdentifier User identifier
	 * @return \TYPO3\Party\Domain\Model\Person
	 */
	public function getUserFromIdentifier($userIdentifier) {
		return $this->personRepository->findByIdentifier($userIdentifier);
	}

	/**
	 * Get all recipients by category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return array
	 */
	public function getAllRecipientsByCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		return $this->personRepository->getRecipientsByCategory($category);
	}

	/**
	 * Email approval by user
	 *
	 * @param \TYPO3\Party\Domain\Model\Person $user User
	 * @return void
	 */
	public function emailApprovalByUser($user) {
		$user->getPrimaryElectronicAddress()->setApproved(TRUE);
		$this->personRepository->update($user);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Send Email
	 *
	 * @param string $url
	 * @param \TYPO3\Party\Domain\Model\Person $user User
	 * @param string $template Template name
	 * @param string $subject Email subject
	 * @return void
	 */
	public function sendEmail($url, $user, $template, $subject) {
		$values = array('name' => $user->getName(), 'url' => $url, 'uid' => $user->getUid(), 'timestamp' => strtotime('now'));
		$recipientAddress = $user->getPrimaryElectronicAddress()->getIdentifier();
		$recipientName = $user->getName();
		$message = $this->emailNotificationService->buildEmailMessage($template, $values);
		$this->emailNotificationService->sendMail($subject, $message, $recipientAddress, $recipientName);
	}

	/**
	 * Update recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$this->personRepository->update($person);
	}

	/**
	 * Delete Related Categories
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return void
	 */
	public function deleteRelatedCategories(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$recipients = $this->personRepository->getRecipientsByCategory($category);
		foreach ($recipients as $recipient) {
			$recipient->removeCategories($category);
			$this->personRepository->update($recipient);
		}
	}

	/**
	 * Delete recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$this->newsletterService->deleteRelatedRecipients($person);
		$this->personRepository->remove($person);
		$this->persistenceManager->persistAll();
	}

}
?>