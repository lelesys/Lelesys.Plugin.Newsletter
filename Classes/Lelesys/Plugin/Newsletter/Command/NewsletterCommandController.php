<?php
namespace Lelesys\Plugin\Newsletter\Command;

/* *
 * This script belongs to the TYPO3 Flow package "Lelesys.Plugin.Newsletter".*
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * A NewsLetter mail Sendout Command Controller
 *
 * @Flow\Scope("singleton")
 */
class NewsletterCommandController extends \Neos\Flow\Cli\CommandController {

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
	 * @var \Lelesys\Plugin\Newsletter\Service\NewsletterBuildService
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
		$emailSentCount = 0;
		$emailLogs = $this->emailLogService->listAllUndeliveredMailsLogs($numberOfEmails, $emailSentCount);
		if ($emailLogs->count() !== 0) {
			$emailSentCount = $this->newsletterBuildService->buildAndSendNewsletter($emailLogs->toArray());
			$this->outputLine('%d Newsletter(s) sent successfully.', array($emailSentCount));
		} else {
			$this->outputLine('There are no any mail to send.');
		}
		unlink($fileName);
	}

}
