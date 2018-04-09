<?php
namespace Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagement\Category;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagementController;
use Lelesys\Plugin\Newsletter\Domain\Model\Category;

/**
 * A Newsletter Category Controller
 *
 * @Flow\Scope("singleton")
 */
class CategoryController extends NewsletterManagementController {

	/**
	 * Category Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\CategoryService
	 */
	protected $categoryService;

	/**
	 * Person Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\PersonService
	 */
	protected $personService;

	/**
	 * Central Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Service\CentralService
	 */
	protected $centralService;

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * List of newsletter categories
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('categories', $this->categoryService->listAll());
	}

	/**
	 * New category
	 *
	 * @return void
	 */
	public function newAction() {

	}

	/**
	 * Displays newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return void
	 */
	public function showAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$newsletters = $this->newsletterService->getAllNewslettersByCategory($category);
		$contentNodes = array();
		foreach ($newsletters as $newsletter) {
			$contentNodes[] = $this->newsletterService->getContentNode($newsletter->getContentNode());
		}
		$this->view->assignMultiple(array(
			'category' => $category,
			'recipients' => $this->personService->getAllRecipientsByCategory($category),
			'contentNodes' => $contentNodes
			)
		);
	}

	/**
	 * Creates new newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $newCategory Newsletter category
	 * @param array $recipients Recipients
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $newCategory, $recipients = array()) {
		try {
			if (isset($recipients['reciver']) === TRUE) {
				$this->categoryService->create($newCategory, $recipients['reciver']);
			} else {
				$this->categoryService->create($newCategory);
			}
			$header = 'Created a new category.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.add.category');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot create category at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.addCategory');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Edit newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return void
	 */
	public function editAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$this->view->assign('category', $category);
		$recipients = $this->personService->listAllSelectedCategory($category);
		$this->view->assign('selectedRecipients', $recipients);
	}

	/**
	 * Update newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @param array $recipients Recipients
	 * @return void
	 */
	public function updateAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category, $recipients = array()) {
		try {
			if (isset($recipients['reciver']) === TRUE) {
				$this->categoryService->update($category, $recipients['reciver']);
			} else {
				$this->categoryService->update($category);
			}
			$header = 'Updated the category.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.update.category');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot update category at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.updatecategory');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return void
	 */
	public function deleteAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		try {
			$this->categoryService->delete($category);
			$header = 'Deleted the category';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.category.delete');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot delete category at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.deletecategory');
			$this->addFlashMessage($message, $header, \Neos\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

}
?>