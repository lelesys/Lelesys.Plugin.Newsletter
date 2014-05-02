<?php
namespace Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagement\Party;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagementController;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party;

/**
 * A Party Controller
 *
 * @Flow\Scope("singleton")
 */
class PartyController extends NewsletterManagementController {

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
	 * List of all parties
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('parties', $this->partyService->listAll());
	}

	/**
	 * Detail of party group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
	 * @return void
	 */
	public function showAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		$this->view->assign('party', $party);
	}

	/**
	 * News party
	 *
	 * @return void
	 */
	public function newAction() {

	}

	/**
	 * Creates a new group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $newParty Party object
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $newParty) {
		try {
			$this->partyService->create($newParty);
			$header = 'Created a new group.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.add.group');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot create group at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.addGroup');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Edit group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
	 * @return void
	 */
	public function editAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		$this->view->assign('party', $party);
	}

	/**
	 * Updates group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
	 * @return void
	 */
	public function updateAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		try {
			$this->partyService->update($party);
			$header = 'Updated group.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.update.group');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot update group at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.updateGroup');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
	 * @return void
	 */
	public function deleteAction(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		try {
			$this->partyService->delete($party);
			$header = 'Deleted the group.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.delete.group');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot delete group at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.deleteGroup');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

}
?>