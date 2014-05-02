<?php
namespace Lelesys\Plugin\Newsletter\Service;

/*                                                                            *
 * This script belongs to the TYPO3 Flow package "Lelesys.Plugin.Newsletter". *
 *                                                                            *
 * It is free software; you can redistribute it and/or modify it under        *
 * the terms of the GNU Lesser General Public License, either version 3       *
 * of the License, or (at your option) any later version.                     *
 *                                                                            */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\SwiftMailer\Message;

/**
 * Service to send Newsletter mails
 *
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
	 * Sends newsletter mail to particular recipients
	 *
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
		$bccAddresses = $this->settings['email']['bccAddresses'];
		$ccAddresses = $this->settings['email']['ccAddresses'];
		$mail = new Message();
		$mail->setFrom(array($fromEmail => $fromName))
				->setSubject($subject)
				->setPriority($priority)
				->setCharset($characterSet)
				->setContentType($contentType);
		if (!empty($attachments)) {
			foreach ($attachments as $path => $fileName) {
				$mail->attach(\Swift_Attachment::fromPath($path)->setFilename($fileName));
			}
		}
		$mail->setReplyTo(array($replyEmail => $replyName))
				->setBcc($bccAddresses)
				->setCc($ccAddresses)
				->setTo($recipientAddress)
				->setBody($message, 'text/html');

		$mail->send();
	}

	/**
	 * Sends test mail of newsletter
	 *
	 * @param string $subject The email subject
	 * @param string $message The message text
	 * @param string $recipientAddress The recipient email address
	 * @param string $recipientName The recipient name
	 * @param array $attachments The email attachments
	 * @param array $bccAddresses Array of bcc email addresses
	 * @param array $ccAddresses Array of cc email addresses
	 * @return void
	 */
	public function sendMail($subject, $message, $recipientAddress, $recipientName, $attachments = NULL, $bccAddresses = array(), $ccAddresses = array()) {
		$mail = new Message();
		$mail
				->setFrom(array($this->settings['email']['senderEmail'] => $this->settings['email']['senderName']))
				->setSubject($subject)
				->setReplyTo($this->settings['email']['replyTo'])
				->setBody($message, 'text/html')
				->setTo(array($recipientAddress => $recipientName))
				->setBcc($bccAddresses)
				->setCc($ccAddresses);
		if (!empty($attachments)) {
			foreach ($attachments as $path => $fileName) {
				$mail->attach(\Swift_Attachment::fromPath($path)->setFilename($fileName));
			}
		}
		$mail->send();
	}

	/**
	 * Bulid a email message
	 *
	 * @param array $values The array of values to be assigned to tmeplate
	 * @param string $format Format of email
	 * @param string $templateName Template name
	 * @return string The message
	 */
	public function buildEmailMessage(array $values, $format = 'txt', $templateName = NULL) {
		$template = new StandaloneView();
		if ($templateName === NULL) {
			$template->setTemplatePathAndFilename($this->settings['email']['template'][$format]['templatePathAndFilename']);
		} else {
			$template->setTemplatePathAndFilename('resource://Lelesys.Plugin.Newsletter/Private/Templates/Emails/' . $templateName);
		}
		$template->assignMultiple($values);
		$template->setFormat($format);
		return $template->render();
	}

}
?>