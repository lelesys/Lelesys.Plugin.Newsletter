<?php

namespace Lelesys\Plugin\Newsletter\Domain\Service;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList;

/**
 * @Flow\Scope("singleton")
 */
class StaticListService {

	/**
	 * StaticList Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\Group\StaticListRepository
	 */
	protected $staticListRepository;

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * List of all staticList group
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList
	 */
	public function listAll() {
		return $this->staticListRepository->findAll();
	}

	/**
	 * Adds new StaticList
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $newStaticList
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $newStaticList) {
		$this->staticListRepository->add($newStaticList);
	}

	/**
	 * Update StaticList group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		$this->staticListRepository->update($staticList);
	}

	/**
	 * Delete StaticList
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList $staticList) {
		$this->newsletterService->deleteRelatedRecipientGroupStaticList($staticList);
		$this->staticListRepository->remove($staticList);
	}

}

?>