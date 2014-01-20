<?php

namespace Lelesys\Plugin\Newsletter\Domain\Service;

/* *
 * This script belongs to the TYPO3 Flow package "Madresfera.Madresfera". *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\SwiftMailer\Message;

/**
 * @Flow\Scope("singleton")
 */
class EmailNotificationService {

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Injects settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Sends mail
	 * @param string $fromEmail The sender address
	 * @param string $fromName The sender name
	 * @param string $replyEmail The reply email
	 * @param string $replyName The replyTo name
	 * @param string $subject The email subject
	 * @param string $priority The email priority
	 * @param string $characterSet The email characterSet
	 * @param array $attachments The email attachments
	 * @param string $contentType The email contentType
	 * @param string $message The message text
	 * @param array $recipientAddress The recipient email address
	 * @param string $recipientName The recipient name
	 * @param array $bccAddresses Array of bcc email addresses
	 * @param array $ccAddresses Array of cc email addresses
	 * @return void
	 */
	public function sendNewsletterMail($fromEmail, $fromName, $replyEmail, $replyName, $subject, $priority = NULL, $characterSet = NULL, $attachments = NULL, $contentType = NULL, $recipientAddress, $message = NULL, $bccAddresses = array(), $ccAddresses = array()) {
		$mail = new Message();
		$mail
				->setFrom(array($fromEmail => $fromName))
				->setSubject($subject)
				->setPriority($priority)
				->setCharset($characterSet)
				->setContentType($contentType);
		if (!empty($attachments)) {
			$mail->attach(\Swift_Attachment::fromPath($attachments['path'])->setFilename($attachments['name']));
		}
		$mail->setReplyTo(array($replyEmail => $replyName))
				->setBcc($bccAddresses)
				->setCc($ccAddresses);
		foreach ($recipientAddress as $recipient) {
			if (isset($recipient[2])) {
				$mail->setTo(array($recipient[0] => $recipient[2]));
			} else {
				$mail->setTo($recipient[0]);
			}
			$mail->setBody($recipient[1]);
			$mail->send();
		}
	}

	/**
	 * Sends mail
	 *
	 * @param string $subject The email subject
	 * @param string $message The message text
	 * @param string $recipientAddress The recipient email address
	 * @param string $recipientName The recipient name
	 * @param array $bccAddresses Array of bcc email addresses
	 * @param array $ccAddresses Array of cc email addresses
	 * @return void
	 */
	public function sendMail($subject, $message, $recipientAddress, $recipientName, $bccAddresses = array(), $ccAddresses = array()) {
		$mail = new Message();
		$mail
				->setFrom(array($this->settings['email']['senderEmail'] => $this->settings['email']['senderName']))
				->setSubject($subject)
				->setReplyTo($this->settings['email']['replyTo'])
				->setBody($message, 'text/html')
				->setTo(array($recipientAddress => $recipientName))
				->setBcc($bccAddresses)
				->setCc($ccAddresses)
				->send();
	}

	/**
	 * Bulid a email message
	 *
	 * @param string $templateName The template filename
	 * @param array $values The array of values to be assigned to tmeplate
	 * @return string The message
	 */
	public function buildEmailMessage($templateName, array $values) {
		$template = new StandaloneView();
		$template->setTemplatePathAndFilename('resource://Lelesys.Plugin.Newsletter/Private/Templates/Emails/' . $templateName);
		$template->assignMultiple($values);
		return $template->render();
	}

}

?>