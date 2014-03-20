<?php
namespace Lelesys\Plugin\Newsletter\ViewHelpers;

/**
 * This script belongs to the Flow package "Lelesys.Plugin.Newsletter".   *
 *                                                                        *
 *                                                                        */
use TYPO3\Flow\Annotations as Flow;

/**
 * A view helper to show news's category and its related newsletter
 *
 * @Flow\scope("prototype")
 */
class RecipientEmailViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * EmailLog Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\EmailLogService
	 */
	protected $emailLogService;

	/**
	 * Render email of recipient
	 *
	 * @return string $content content to render
	 */
	public function render() {
		$identifier = trim($this->renderChildren());
		$isValid = filter_var($identifier, FILTER_VALIDATE_EMAIL);

		if ($isValid === FALSE) {
			$email = $this->emailLogService->getRecipientEmail($identifier);
			if ($email) {
				return $email[0]['identifier'];
			}
		} else {
			return $identifier;
		}
	}

}
?>