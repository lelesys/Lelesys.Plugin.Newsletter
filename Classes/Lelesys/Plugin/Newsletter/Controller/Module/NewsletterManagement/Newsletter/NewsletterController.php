<?php
namespace Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagement\Newsletter;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Controller\Module\NewsletterManagementController;
use TYPO3\Flow\Mvc\Routing\UriBuilder;

/**
 * A Newsletter Controller to manage all CRUD methods of newsletter
 *
 * @Flow\Scope("singleton")
 */
class NewsletterController extends NewsletterManagementController {

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * Category Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\CategoryService
	 */
	protected $categoryService;

	/**
	 * AbstractGroup Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\AbstractGroupService
	 */
	protected $abstractGroupService;

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
	 * List of newsletters
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->newsletterService->checkDeletedNodeFromNewsletter();
		$this->view->assign('newsletters', $this->newsletterService->listAll());
	}

	/**
	 * Detail of newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @return void
	 */
	public function showAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$recipientsByGroups = $this->newsletterService->getAllRecipientsByGroups($newsletter);
		$this->view->assignMultiple(array(
			'recipientsByGroups' => $recipientsByGroups,
			'newsletter' => $newsletter,
			'GroupParty' => $recipientsByGroups['GroupParty'],
			'GroupStatic' => $recipientsByGroups['GroupStatic'],
			'contentNode' => $this->newsletterService->getContentNode($newsletter->getContentNode())
			)
		);
	}

	/**
	 * New newsletter
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\Node $currentNode Current Node
	 * @return void
	 */
	public function newAction() {
		$this->view->assign('contentNode', $this->newsletterService->getAllNewsletterChildNodes());
		$this->view->assign('recipientGroups', $this->abstractGroupService->listAll());
		$this->view->assign('categories', $this->categoryService->listAll());
		$this->view->assign('recipients', $this->personService->listAllApproved());
	}

	/**
	 * Sends newsletter email
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter The newsletter
	 * @param string $adminEmail Admin email
	 * @return void
	 */
	public function sendEmailAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $adminEmail = NULL) {
		try {
			if ($newsletter->getContentNode() !== NULL) {
				if ($adminEmail !== NULL) {
					$this->newsletterService->sendTestEmail($adminEmail, $newsletter);
					$header = 'Test email sent';
					$message = $this->centralService->translate('lelesys.plugin.newsletter.testEmailSent');
					$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
				} else {
					$recipientGroups = $newsletter->getRecipientGroups();
					$count = 0;
					foreach ($recipientGroups as $recipientGroup) {
						$recipient = count($recipientGroup->getRecipients());
						$count = $count + $recipient;
					}
					$categoryRecipients = $this->personService->findByRecipientsByCategories($newsletter);
					$count += count($categoryRecipients);
					if (($count < 1) && (count($newsletter->getRecipients()) < 1 ) && count($categoryRecipients) < 1) {
							$header = 'Newsletter does not have any subscribers';
							$message = $this->centralService->translate('lelesys.plugin.newsletter.noUsers');
							$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
						}
						else {
							$newsletter->setHtmlBody(NULL);
							$newsletter->setPlainTextBody(NULL);
							$this->newsletterService->update($newsletter, array());
							$this->newsletterService->sendEmail($newsletter);
							$header = 'Added in Email Log';
							$message = $this->centralService->translate('lelesys.plugin.newsletter.addedLog');
							$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
						}
				}
			} else {
				$header = 'No page selected for newsletter.Can not send email.';
				$message = $this->centralService->translate('lelesys.plugin.newsletter.nopage');
				$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
			}
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot send newsetter at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.sendEmail');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Creates a new newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter Newsletter object
	 * @param array $attachments Attachments
	 * @return void
	 */
	public function createAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newNewsletter, $attachments = array()) {
		try {
			$this->newsletterService->create($newNewsletter, $attachments);
			$header = 'Created a new newsletter';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.add.newsletter');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot create newsletter at this time!!';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.addNewsletter');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Edit of newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @return void
	 */
	public function editAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$this->view->assign('categories', $this->categoryService->listAll());
		$this->view->assign('recipientGroups', $this->abstractGroupService->listAll());
		$this->view->assign('contentNode', $this->newsletterService->getAllNewsletterChildNodes());
		$this->view->assign('recipients', $this->personService->listAllApproved());
		$this->view->assign('newsletter', $newsletter);
	}

	/**
	 * Update newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @param array $attachments Attachments
	 * @return void
	 */
	public function updateAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, $attachments = array()) {
		try {
			$newsletter->setHtmlBody(NULL);
			$newsletter->setPlainTextBody(NULL);
			$this->newsletterService->update($newsletter, $attachments);
			$header = 'Updated newsletter';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.update.newsletter');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot update newsletter at this time!!';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.updateNewsletter');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @return void
	 */
	public function deleteAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		try {
			$this->newsletterService->delete($newsletter);
			$header = 'Deleted newsletter.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.delete.newsletter');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Cannot delete newsletter at this time!!.';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.cannot.deleteNewsletter');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('index');
	}

	/**
	 * Delete newsletter attachment
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter object
	 * @param \TYPO3\Media\Domain\Model\Document $attachment
	 * @return void
	 */
	public function deleteAttachmentAction(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter, \TYPO3\Media\Domain\Model\Document $attachment) {
		try {
			$this->newsletterService->deleteAttachment($newsletter, $attachment);
			$header = 'Deleted attachment';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.deleteAttachmentSuccessful');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_OK);
		} catch (Lelesys\Plugin\Newsletter\Domain\Service\Exception $exception) {
			$header = 'Could not delete attachment';
			$message = $this->centralService->translate('lelesys.plugin.newsletter.deleteAttachmentFail');
			$this->addFlashMessage($message, $header, \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		}
		$this->redirect('edit', '', '', array('newsletter' => $newsletter));
	}
}
?>