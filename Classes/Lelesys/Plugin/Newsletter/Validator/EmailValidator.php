<?php
namespace Lelesys\Plugin\Newsletter\Validator;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".                   *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;

/**
 * Validator for validating multiple emails
 *
 * @api
 */
class EmailValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {

	/**
	 * Inject translator
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Translator
	 */
	protected $translator;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject settings
	 *
	 * @param array $settings Settings array
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Returns TRUE, if the given property ($value) matches the session captcha Value.
	 *
	 * If at least one error occurred, the result is FALSE.
	 *
	 * @param mixed $value The value that should be validated
	 * @return boolean TRUE if the value is valid, FALSE if an error occured
	 */
	public function isValid($value) {
		$emails[] = explode(',', $value);
		foreach ($emails[0] as $email) {
			$validEmail = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
			if ($validEmail == FALSE) {
				$this->addError('Please enter valid email Address', 1324641097);
				return;
			}
		}
	}

}
?>