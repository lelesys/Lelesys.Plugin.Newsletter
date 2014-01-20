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
use TYPO3\Flow\Mvc\Routing\UriBuilder;

/**
 * A Newsletter Controller
 *
 * @Flow\Scope("singleton")
 */
class NewsletterController extends ActionController {

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * Creates a new newsletter page node
	 *
	 * @return void
	 */
	public function createNewsletterAction() {
		$newsletterDocumentNode = $this->request->getInternalArgument('__documentNode');
		if ($newsletterDocumentNode === NULL) {
			return 'Error: The Newsletter Plugin cannot determine the current document node. Please make sure to include this plugin only by inserting it into a page / document.';
		}
		$newsletterNode = $this->newsletterService->createNewsletter($newsletterDocumentNode);
		$mainRequest = $this->request->getMainRequest();
		$mainUriBuilder = new UriBuilder();
		$mainUriBuilder->setRequest($mainRequest);
		$mainUriBuilder->setFormat('html');
		$uri = $mainUriBuilder
				->reset()
				->setCreateAbsoluteUri(TRUE)
				->uriFor('show', array('node' => $newsletterNode));
		$this->redirectToUri($uri);
	}

	/**
	 * Displays plus icon to create newsletter node
	 *
	 * @return void
	 */
	public function newNewsletterNodeAction() {

	}

}

?>