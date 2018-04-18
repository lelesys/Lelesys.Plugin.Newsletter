<?php
namespace Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagement\StaticList;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagementController;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList;

/**
 * A StaticList Controller
 *
 * @Flow\Scope("singleton")
 */
class StaticListController extends NewsletterManagementController {

	/**
	 * StaticList Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\StaticListService
	 */
	protected $staticListService;

	/**
	 * Central Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Service\CentralService
	 */
	protected $centralService;

	/**
	 * List of StaticLists group
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('staticLists', $this->staticListService->listAll());
	}

	/**
	 * Detail of staticList group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function showAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		$this->view->assign('staticList', $staticList);
	}

	/**
	 * News staticList group
	 *
	 * @return void
	 */
	public function newAction() {

	}

	/**
	 * Creates new staticList group
	 * @Flow\Validate(argumentName="newStaticList.recipientList", type="\Lelesys\Plugin\Newsletter\Validator\EmailValidator")
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $newStaticList
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $newStaticList) {
		try {
			$this->staticListService->create($newStaticList);
			$header = 'Created a new group.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.add.group');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot create group at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.addGroup');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Edit of staticList group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function editAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		$this->view->assign('staticList', $staticList);
	}

	/**
	 * Updates staticList group
	 *
	 * @Flow\Validate(argumentName="staticList.recipientList", type="\Lelesys\Plugin\Newsletter\Validator\EmailValidator")
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function updateAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		try {
			$this->staticListService->update($staticList);
			$header = 'Updated group.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.update.group');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot update group at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.updateGroup');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete staticList group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function deleteAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		try {
			$this->staticListService->delete($staticList);
			$header = 'Deleted the group.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.delete.group');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot delete group at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.deleteGroup');
			$this->addFlashMessage($message, $header, \Neos\Error\Messages\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

}
?>