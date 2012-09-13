<?php
namespace MenuLib\MenuItemRenderer;

/**
 *
 */
abstract class BaseMenuItemRenderer implements MenuItemRendererInterface {
	/**
	 * @var array
	 */
	public $settings = array();

	/**
	 * @param array $settings
	 */
	protected function __construct($settings = array()) {
		$this->settings = array_merge($this->settings, $settings);
	}
}

?>