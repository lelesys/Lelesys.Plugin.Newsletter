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
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup;

/**
 * A AbstractGroup Controller
 *
 * @Flow\Scope("singleton")
 */
class AbstractGroupController extends ActionController {

	/**
	 * AbstractGroup Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\AbstractGroupRepository
	 */
	protected $abstractGroupRepository;

	/**
	 * List of groups
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('abstractGroups', $this->abstractGroupRepository->findAll());
	}

	/**
	 * Detail of abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup
	 * @return void
	 */
	public function showAction(AbstractGroup $abstractGroup) {
		$this->view->assign('abstractGroup', $abstractGroup);
	}

	/**
	 * New abstract group
	 *
	 * @return void
	 */
	public function newAction() {

	}

	/**
	 * Creates new abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $newAbstractGroup
	 * @return void
	 */
	public function createAction(AbstractGroup $newAbstractGroup) {
		$this->abstractGroupRepository->add($newAbstractGroup);
		$this->addFlashMessage('Created a new abstract group.');
		$this->redirect('index');
	}

	/**
	 * Edit abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup
	 * @return void
	 */
	public function editAction(AbstractGroup $abstractGroup) {
		$this->view->assign('abstractGroup', $abstractGroup);
	}

	/**
	 * Update abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup
	 * @return void
	 */
	public function updateAction(AbstractGroup $abstractGroup) {
		$this->abstractGroupRepository->update($abstractGroup);
		$this->addFlashMessage('Updated the abstract group.');
		$this->redirect('index');
	}

	/**
	 * Delete abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup
	 * @return void
	 */
	public function deleteAction(AbstractGroup $abstractGroup) {
		$this->abstractGroupRepository->remove($abstractGroup);
		$this->addFlashMessage('Deleted a abstract group.');
		$this->redirect('index');
	}

}

?>