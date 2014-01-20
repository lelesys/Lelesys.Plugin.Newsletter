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
use Lelesys\Plugin\Newsletter\Domain\Model\EmailLog;
/**
 * Service for sending Email Notifications
 *
 * @Flow\Scope("singleton")
 */
class EmailLogService {

	/**
	 * EmailLog Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\EmailLogRepository
	 */
	protected $emailLogRepository;

	/**
	 * List pf all EmailLog
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog
	 */
	public function listAll() {
		return $this->emailLogRepository->findAll();
	}

	/**
	 * Adds EmailLog
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $newEmailLog
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $newEmailLog) {
		$this->emailLogRepository->add($newEmailLog);
	}

	/**
	 * Updates EmailLog
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog) {
		$this->emailLogRepository->update($emailLog);
	}

	/**
	 * Deletes EmailLog
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog) {
		$this->emailLogRepository->remove($emailLog);
	}

}

?>