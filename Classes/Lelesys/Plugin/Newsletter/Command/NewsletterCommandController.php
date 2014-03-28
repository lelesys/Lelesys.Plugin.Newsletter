<?php
namespace Lelesys\Plugin\Newsletter\Command;

/* *
 * This script belongs to the TYPO3 Flow package "Lelesys.Plugin.Newsletter".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A NewsLetter mail Sendout Command Controller
 *
 * @Flow\Scope("singleton")
 */
class NewsletterCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * EmailLog Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailLogService
	 */
	protected $emailLogService;

	/**
	 * Newsletter Build Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterBuildService
	 */
	protected $newsletterBuildService;

	/**
	 * Inject Settings
	 *
	 * @param array $settings Settings array
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * A NewsLetter mail Sendout Command
	 *
	 * This command goes through EmailLog
	 * and send the mail to particular recipient to which mail is not delivered
	 * After sending mail the entries in Emaillog is upadated
	 *
	 * @param integer $numberOfEmails Number of emails per cycle
	 * @return void
	 */
	public function sendoutCommand($numberOfEmails = 0) {
		if ($numberOfEmails === 0) {
			$numberOfEmails = $this->settings['noOfEmails'];
		}
		\clearstatcache();
		$fileName = FLOW_PATH_DATA . 'Persistent/emailLog.lock';
		if (is_file($fileName)) {
			if (filemtime($fileName) > (time() - (60*60*24))) {
				$this->outputLine('Another queue is running... so quiting');
				$this->quit();
			}
		}
		touch($fileName);
		$totalMailsCount = $this->emailLogService->findCountOfMailsLogs();
		$currentTime = new \DateTime();
		if ($totalMailsCount !== 0) {
			$emailSentCount = 0;
			while ($totalMailsCount > 0) {
				$totalMails = $this->emailLogService->listAllUndeliveredMailsLogs($numberOfEmails, $emailSentCount);
				foreach ($totalMails as $emailLog) {
					$this->newsletterBuildService->buildMail($emailLog);
					$emailLog->setIsSent(1);
					$emailLog->setTimeSent($currentTime);
					$this->emailLogService->update($emailLog);
					$totalMailsCount--;
					$emailSentCount++;
				}
			}
			if ($emailSentCount === 1) {
				$this->outputLine('Mail is sent successfully to particular recipient');
			} else {
				$this->outputLine('Mails are sent successfully to particular recipients');
			}
			unlink($fileName);
		} else {
			$this->outputLine('There are no any mail to send.');
			unlink($fileName);
		}
	}

}
