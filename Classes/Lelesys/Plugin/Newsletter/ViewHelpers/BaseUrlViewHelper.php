<?php
namespace Lelesys\Plugin\Newsletter\ViewHelpers;

use Neos\Flow\Annotations as Flow;

/**
 * A view helper to display Base Url
 *
 * @Flow\scope("prototype")
 */
class BaseUrlViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject settings
	 *
	 * @param array $settings Settings Array
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Shows the base uri from settings
	 *
	 * @return string
	 */
	public function render() {
		$baseUrl = $this->settings['email']['baseUrl'];
		return $baseUrl;
	}

}
?>