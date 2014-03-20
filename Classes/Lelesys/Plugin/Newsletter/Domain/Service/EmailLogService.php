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
use Lelesys\Plugin\Newsletter\Domain\Model\EmailLog;

/**
 * Service for storing Email logs
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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * List pf all EmailLog
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog
	 */
	public function listAll() {
		return $this->emailLogRepository->findAll();
	}

	/**
	 * List pf all EmailLogs for undelivered mailes
	 * List all logs which has value isFailed=TRUE
	 *
	 * @param integer $limit Limit of emails
	 * @param integer $offset Offset of emails
	 * @return TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function listAllUndeliveredMailsLogs($limit, $offset) {
		return $this->emailLogRepository->findAllUndeliveredMailsLogs($limit, $offset);
	}

	/**
	 * Total number of email logs
	 *
	 * @return integer Total number of mail logs to be sent
	 */
	public function findCountOfMailsLogs() {
		return $this->emailLogRepository->findCountOfMailsLogs();
	}

	/**
	 * Create EmailLog
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @param string $recipientType Recipient type
	 * @param string $recipients Recipients
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $recipientType, $recipient = array()) {
		$newEmailLog = new \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog();
		$newEmailLog->setNewsletter($newsletter);
		$newEmailLog->setRecipientType($recipientType);
		$newEmailLog->setRecipient($recipient);
		$newEmailLog->setIsSent(FALSE);
		$newEmailLog->setIsFailed(FALSE);
		$this->emailLogRepository->add($newEmailLog);
	}

	/**
	 * Updates EmailLog
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog EmailLog object
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog) {
		$this->emailLogRepository->update($emailLog);
	}

	/**
	 * Finds recipient email address
	 *
	 * @param string $identifier Person identifier
	 * @return array
	 */
	public function getRecipientEmail($identifier) {
		return $this->emailLogRepository->getRecipientEmail($identifier);
	}

}
?>