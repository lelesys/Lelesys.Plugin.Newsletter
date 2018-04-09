<?php
namespace Lelesys\Plugin\Newsletter\Controller;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
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
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Service\CentralService
	 */
	protected $centralService;

	/**
	 * The configuration content dimension preset source
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Neos\Domain\Service\ConfigurationContentDimensionPresetSource
	 */
	protected $configurationContentDimensionPresetSource;

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
	 * @Flow\Validate(type="NotEmpty",value="newPerson.primaryElectronicAddress.identifier")
	 * @Flow\Validate(type="EmailAddress",value="newPerson.primaryElectronicAddress.identifier")
	 * @Flow\Validate(type="NotEmpty",value="newPerson.name.firstName")
	 * @Flow\Validate(type="NotEmpty",value="newPerson.name.lastName")
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson The recipient
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson) {
		try {
			$baseUri = $this->request->getHttpRequest()->getBaseUri();
			$isExistingUser = $this->personService->isExistingUser($newPerson);
			$pluginNode = $this->request->getInternalArgument('__node');
			$dimensions = $pluginNode->getContext()->getDimensions();
			$currentLocale = $this->configurationContentDimensionPresetSource->findPresetByDimensionValues('language', $dimensions['language']);
			if (($isExistingUser !== NULL) && ($isExistingUser === TRUE)) {
				$header = 'This email address has already subscribed!';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.emailExist');
				$this->addFlashMessage($newPerson->getPrimaryElectronicAddress()->getIdentifier() . $message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
				$this->redirect("new");
			} else {
				$this->personService->create($newPerson, $currentLocale['identifier']);
				$header = 'Thank you for subscribing to our newsletter.';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.subscribed');
				$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_OK);
				$this->redirect("new");
			}
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Can not subscribe to newsletter at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.subscribe');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
			$this->redirect("new");
		}
		$this->redirectToUri($baseUri);
	}

	/**
	 * Unsubscribe confirmation of newsletter
	 *
	 * @param string $recipientId To unsubcribe
	 * @param string $code Code
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
	 * User confirmation after registration
	 *
	 * @return array
	 */
	public function subscriptionConfirmationAction() {
		$recipientId = $this->request->getHttpRequest()->getArgument('recipientId');
		$code = $this->request->getHttpRequest()->getArgument('code');
		if (empty($recipientId) === FALSE) {
			$this->redirect('unSubscribeConfirmation', NULL, NULL, array('recipientId' => $recipientId, 'code' => $code));
		}
		$userIdentifier = $this->request->getHttpRequest()->getArgument('user');
		$isConfirmed = $this->personService->confirmSubscription($code, $userIdentifier);
		if ($isConfirmed === 1) {
			$header = 'The user is approved!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.user.approved');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_OK);
		} elseif ($isConfirmed === 0) {
			$header = 'Link not valid!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.linkNotValid');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
		} elseif ($isConfirmed === 2) {
			$header = 'Already confirmed user!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.user.confirmed');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
		}
	}

	/**
	 * UnSubscribe newsletter
	 *
	 * @param string $recipient To unsubcribe
	 * @param string $code Code
	 * @return void
	 */
	public function unSubscribeAction($recipient, $code) {
		$unSubscribed = $this->personService->unSubscribe($recipient, $code);
		$baseUri = $this->request->getHttpRequest()->getBaseUri();
		if ($unSubscribed === 1) {
			$header = 'You are now unsubscribed!';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.user.unsubscribed');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_OK);
		} elseif ($unSubscribed === 0) {
			$header = 'Link not valid!';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.linkNotValid');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirectToUri($baseUri);
	}

	/**
	 * No flash message should be set
	 *
	 * @return boolean
	 * @api
	 */
	protected function getErrorFlashMessage() {
		return FALSE;
	}
}
?>