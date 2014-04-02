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
use Lelesys\Plugin\Newsletter\Domain\Model\Newsletter;
use TYPO3\TYPO3CR\Domain\Model\NodeTemplate;
use TYPO3\TYPO3CR\Domain\Model\PersistentNodeInterface;
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TYPO3\Flow\Http\Request as Request;

/**
 * Newsletter Build Service
 *
 * @Flow\Scope("singleton")
 */
class NewsletterBuildService {


	/**
	 * Email Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailNotificationService
	 */
	protected $emailNotificationService;

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * NodeData Repository
	 *
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository
	 */
	protected $nodeDataRepository;

	/**
	 * Person Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\PersonRepository
	 */
	protected $personRepository;

	/**
	 * ResourceManager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\ResourceManager
	 */
	protected $resourceManager;

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Contextfactory
	 *
	 * @Flow\Inject
	 * @var TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface
	 */
	protected $contextFactory;

	/**
	 * Inject ConfigurationManager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Inject RouterInterface
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * Inject Dispatcher
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * The security conntext
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * Inject ObjectManagerInterface
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * Injects settings
	 *
	 * @param array $settings Inject settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Building Mail
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog Emaillog object
	 * @return void
	 */
	public function buildMail(\Lelesys\Plugin\Newsletter\Domain\Model\EmailLog $emailLog) {
		$recipientAddress = array();
		$baseUrl = $this->settings['email']['baseUrl'];
		$newsletter = $emailLog->getNewsletter();
		$fromEmail = $newsletter->getFromEmail();
		$fromName = $newsletter->getFromName();
		$subject = $newsletter->getSubject();
		$priority = $newsletter->getPriority();
		$characterSet = $newsletter->getCharacterSet();
		$replyEmail = $newsletter->getReplyToEmail();
		$replyName = $newsletter->getReplyToName();
		$newsletterAttachments = $newsletter->getAttachments();
		$attachments = array();
		foreach($newsletterAttachments as $newsletterAttachment) {
			$attachments[$this->resourceManager->getPersistentResourcesStorageBaseUri() . $newsletterAttachment->getResource()->getResourcePointer()->getHash()] = $newsletterAttachment->getTitle();
		}

		if ($emailLog->getRecipientType() == \Lelesys\Plugin\Newsletter\Domain\Model\EmailLog::RECIPIENT_TYPE_PERSON) {
			$recipientId = $emailLog->getRecipient();
			$recipient = $this->personRepository->findByIdentifier($recipientId);
			if ($recipient->getAcceptsHtml() === TRUE) {
				$code = sha1($recipient->getPrimaryElectronicAddress()->getIdentifier() . $recipient->getUuid());
				$contentType['html'] = 'text/html';
				$message = $this->buildMailContents('Newsletter.html', array('recipientId' => $recipient->getUuid(), 'code' => $code), $newsletter, $contentType);

				$recipientAddress['html'][] = array($recipient->getPrimaryElectronicAddress()->getIdentifier(), $message, $recipient->getName()->getFirstName(),);
			} else {
				$code = sha1($recipient->getPrimaryElectronicAddress()->getIdentifier() . $recipient->getUuid());
				$contentType['text'] = 'text/plain';

				$message = $this->buildMailContents('Newsletter.txt', array('recipientId' => $recipient->getUuid(), 'code' => $code, 'baseUrl' => $baseUrl . $this->settings['email']['unSubscribeLink']), $newsletter, $contentType);
				$recipientAddress['text'][] = array($recipient->getPrimaryElectronicAddress()->getIdentifier(), $message, $recipient->getName()->getFirstName());
			}
		} else {
			$recipient = $emailLog->getRecipient();
			$contentType['text'] = 'text/plain';

			$message = $this->buildMailContents('Newsletter.txt', array('recipient' => $recipient), $newsletter, $contentType);
			$recipientAddress['text'][] = array(trim($recipient), $message);
		}
		if (isset($contentType['html'])) {
			$this->emailNotificationService->sendNewsletterMail($fromEmail, $fromName, $replyEmail, $replyName, $subject, $priority, $characterSet, $attachments, $contentType['html'], $recipientAddress['html']);
		}
		if (isset($contentType['text'])) {
			$this->emailNotificationService->sendNewsletterMail($fromEmail, $fromName, $replyEmail, $replyName, $subject, $priority, $characterSet, $attachments, $contentType['text'], $recipientAddress['text']);
		}
	}

	/**
	 * Bulid a email message
	 *
	 * @param string $templateName The template filename
	 * @param array $values The array of values to be assigned to tmeplate
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter
	 * @param array $contentType ContentType Text or Html
	 * @return string The message
	 */
	public function buildMailContents($templateName, array $values, \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $contentType = array()) {
		$baseUrl = $this->settings['email']['baseUrl'];
		$nodeData = $this->newsletterService->getContentNode($newsletter->getContentNode());
		$contextFactory = $this->createContext();
		$node = new Node($nodeData, $contextFactory);
		$nodeIdentifier = $node->getIdentifier();
		$routesConfiguration = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
		$this->router->setRoutesConfiguration($routesConfiguration);
		$uri = new \TYPO3\Flow\Http\Uri($baseUrl);
		$httpRequest = Request::create($uri);
		$actionRequest = $httpRequest->createActionRequest();
		$actionRequest->setArgument('node', $nodeIdentifier);
		$newActionRequest = $this->router->route($httpRequest);
		$actionRequest->setControllerPackageKey($newActionRequest->getControllerPackageKey());
		$actionRequest->setControllerName($newActionRequest->getControllerName());
		$actionRequest->setControllerActionName($newActionRequest->getControllerActionName());

		foreach ($contentType as $content) {
			if ($content === 'text/html') {
				$actionRequest->setFormat('html');
				$format = 'html';
			} else {
				$actionRequest->setFormat('txt');
				$format = 'txt';
			}
		}

		$this->securityContext->setRequest($actionRequest);
		$response = $this->objectManager->get('\TYPO3\Flow\Http\Response');
		$this->dispatcher->dispatch($actionRequest, $response);
		$response->makeStandardsCompliant($httpRequest);
		$output = $response->getContent();
		if ($format === 'txt') {
			$output = strip_tags($output,'<a>');
		}
		$values['mailContent'] = $output;
		return $this->emailNotificationService->buildEmailMessage($templateName, $values, $format);
	}


	/**
	 * Create Context
	 *
	 * @return \TYPO3\TYPO3CR\Domain\Service\ContextInterface
	 */
	public function createContext() {
		return $this->contextFactory->create(array(
					'workspaceName' => 'live',
					'invisibleContentShown' => TRUE,
					'inaccessibleContentShown' => TRUE
		));
	}

}
?>