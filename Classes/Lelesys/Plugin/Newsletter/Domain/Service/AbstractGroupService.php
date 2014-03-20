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
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup;

/**
 * Abstract Group Service
 *
 * @Flow\Scope("singleton")
 */
class AbstractGroupService {

	/**
	 * AbstractGroup Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\AbstractGroupRepository
	 */
	protected $abstractGroupRepository;

	/**
	 * AbstractGroup
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup
	 */
	public function listAll() {
		return $this->abstractGroupRepository->findAll();
	}

	/**
	 * List of all recipients
	 *
	 * @return void
	 */
	public function getRecipients() {
		return $this->abstractGroupRepository->getRecipients();
	}

	/**
	 * Adds newAbstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $newAbstractGroup AbstractGroup object
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $newAbstractGroup) {
		$this->abstractGroupRepository->add($newAbstractGroup);
	}

	/**
	 * Updates abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup AbstractGroup object
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup) {
		$this->abstractGroupRepository->update($abstractGroup);
	}

	/**
	 * Deletes abstractGroup
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup AbstractGroup object
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\AbstractGroup $abstractGroup) {
		$this->abstractGroupRepository->remove($abstractGroup);
	}

}
?>