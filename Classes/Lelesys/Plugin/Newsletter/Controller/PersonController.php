<?php

namespace Lelesys\Plugin\Newsletter\Controller;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person;

/**
 * A Person Controller
 *
 * @Flow\Scope("singleton")
 */
class PersonController extends ActionController {

	/**
	 * Person Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\PersonService
	 */
	protected $personService;

	/**
	 * Category Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\CategoryService
	 */
	protected $categoryService;

	/**
	 * Party Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\PartyService
	 */
	protected $partyService;

	/**
	 * Central Service
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\CentralService
	 */
	protected $centralService;

	/**
	 * New recipient
	 *
	 * @return void
	 */
	public function newAction() {
		$this->view->assign('categories', $this->categoryService->listAll());
	}

	/**
	 * Creates new recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson The recipient
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson) {
		try {
			$baseUri = $this->request->getHttpRequest()->getBaseUri();
			$isExistingUser = $this->personService->isExistingUser($newPerson);
			if (($isExistingUser !== NULL) && ($isExistingUser === 1)) {
				$header = 'This email address has already subscribed!';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.emailExist');
				$this->addFlashMessage('' . $newPerson->getPrimaryElectronicAddress()->getIdentifier() . $message . '', \TYPO3\Flow\Error\Message::SEVERITY_OK);
			} else {
				$this->personService->create($newPerson);
				$header = 'Thank you for subscribing to our newsletter.';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.subscribed');
				$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
			}
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot subscribe to newslette at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.subscribe');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirectToUri($baseUri);
	}

	/**
	 * Unsubscribe confirmation of newsletter
	 *
	 * @param string $recipientId To unsubcribe
	 * @param string $code
	 * @return void
	 */
	public function unSubscribeConfirmationAction($recipientId, $code) {
		if ($recipientId !== NULL) {
			$recipient = $this->personService->getUserFromIdentifier($recipientId);
		}
		$this->view->assign('recipient', $recipient);
		$this->view->assign('code', $code);
	}

	/**
	 * Subscription confirmation
	 *
	 * User confirmation after registration
	 * @return array
	 */
	public function subscriptionConfirmationAction() {
		$code = $this->request->getHttpRequest()->getArgument('code');
		$userIdentifier = $this->request->getHttpRequest()->getArgument('user');
		$isConfirmed = $this->personService->confirmSubscription($code, $userIdentifier);
		if ($isConfirmed === 1) {
			$header = 'The user is approved!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.user.approved');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
			throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
		} elseif ($isConfirmed === 0) {
			$header = 'Link not valid!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.linkNotValid');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
			throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
		} elseif ($isConfirmed === 2) {
			$header = 'Already confirmed user!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.user.confirmed');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
			throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
		}
	}

	/**
	 * UnSubscribe newsletter
	 *
	 * @param string $recipient To unsubcribe
	 * @param string $code
	 * @return void
	 */
	public function unSubscribeAction($recipient, $code) {
		$unSubscribed = $this->personService->unSubscribe($recipient, $code);
		$baseUri = $this->request->getHttpRequest()->getBaseUri();
		if ($unSubscribed === 1) {
			$header = 'You are now unsubscribed!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.user.unsubscribed');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} elseif ($unSubscribed === 0) {
			$header = 'Link not valid!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.linkNotValid');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirectToUri($baseUri);
	}

}

?>