<?php

namespace Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagement\Category;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagementController;
use Lelesys\Plugin\Newsletter\Domain\Model\Category;

/**
 * A Category Controller
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
	 * Category Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\CategoryRepository
	 */
	protected $categoryRepository;

	/**
	 * Central Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\CentralService
	 */
	protected $centralService;

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
	 * Creates new newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $newCategory
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $newCategory) {
		try {
			$this->categoryService->create($newCategory);
			$header = 'Created a new category.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.add.category');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot create category at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.addCategory');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Edit newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category
	 * @return void
	 */
	public function editAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$this->view->assign('category', $category);
	}

	/**
	 * Update newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category
	 * @return void
	 */
	public function updateAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		try {
			$this->categoryService->update($category);
			$header = 'Updated the category.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.update.category');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot update category at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.updatecategory');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category
	 * @return void
	 */
	public function deleteAction(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		try {
			$this->categoryService->delete($category);
			$header = 'Deleted the category';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.category.delete');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot delete category at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.deletecategory');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

}

?>