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

/**
 * Central Service
 *
 * @Flow\Scope("singleton")
 */
class CentralService {

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
	 * Inject translator
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Translator
	 */
	protected $translator;

	/**
	 * Returns translated text for given label
	 *
	 * @param string $label Label
	 * @return string Translated string
	 */
	public function translate($label) {
		return $this->translator->translateById($label, array(), NULL, NULL, 'Main', $this->settings['flashMessage']['packageKey']);
	}

}
?>