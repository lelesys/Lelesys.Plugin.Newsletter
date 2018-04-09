<?php
namespace Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagement\Person;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagementController;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person;

/**
 * A Person Controller
 *
 * @Flow\Scope("singleton")
 */
class PersonController extends NewsletterManagementController {

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
	 * AbstractGroup Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\AbstractGroupService
	 */
	protected $abstractGroupService;

	/**
	 * Central Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Service\CentralService
	 */
	protected $centralService;

	/**
	 * EmailLog Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailLogService
	 */
	protected $emailLogService;

	/**
	 * List of recipients
	 *
	 * @return void
	 */
	public function indexAction() {
		$params = $this->request->getArguments();
		$search = '';
		$count = 10;
		if($this->request->hasArgument('recipient-search-submit')) {
			$search = $this->request->getArgument('search');
		}
		if($this->request->hasArgument('recipientCount')) {
			$count = $this->request->getArgument('recipientCount');
		}
		$this->view->assign('recipientCount', $count);
		$this->view->assign('search', $search);
		$this->view->assign('recipients', $this->personService->findBySubscribedToNewsletter($search));
	}

	/**
	 * Detail of recipients
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function showAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$this->view->assign('person', $person);
	}

	/**
	 * News recipient
	 *
	 * @return void
	 */
	public function newAction() {
		$groups = $this->partyService->listAll();
		$this->view->assign('groups', $groups);
		$this->view->assign('categories', $this->categoryService->listAll());
	}

	/**
	 * Creates new recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson Person object
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson) {
		try {
			$isExistingUser = $this->personService->isExistingUser($newPerson);
			if (($isExistingUser !== NULL) && ($isExistingUser === TRUE)) {
				$header = 'This email address has already subscribed!';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.emailExist');
				$this->addFlashMessage('' . $newPerson->getPrimaryElectronicAddress()->getIdentifier() . $message . '', $header , \Neos\Error\Messages\Message::SEVERITY_ERROR);
			} else {
				$this->personService->create($newPerson);
				$header = 'Created a new recipient.';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.add.recipient');
				$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_OK);
			}
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot create recipient at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.addRecipient');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Edit recipient info
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function editAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$this->view->assign('groups', $this->partyService->listAll());
		$this->view->assign('categories', $this->categoryService->listAll());
		$this->view->assign('person', $person);
	}

	/**
	 * Update recipient info
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function updateAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		try {
			$this->personService->update($person);
			$header = 'Updated the recipient info.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.update.recipient');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot update recipient at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.updateRecipient');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function deleteAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		try {
			$this->emailLogService->updateRecipient($person);
			$this->personService->deleteRecipient($person);
			$header = 'Deleted the recipient.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.recipient.delete');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot deleted recipient at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.deleteRecipient');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Find recipient
	 *
	 * @return void
	 */
	public function findRecipientByNameAction() {
		$param = $this->request->getHttpRequest()->getArguments();
		$terms = $param['term'];
		$result = $this->personService->findBySubscribedToNewsletter($terms);
		$returnArray = array();
		foreach($result as $recipient) {
			$a = array();
			$a['id'] = $recipient->getUuid();
			$a['label'] = $recipient->getName()->getFullName();
			$a['value'] = $recipient->getName()->getFullName();
			$returnArray[] = $a;
		}
		echo json_encode($returnArray);exit();
	}

}
?>