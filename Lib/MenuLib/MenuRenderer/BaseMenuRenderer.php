<?php
namespace MenuLib\MenuRenderer;

use MenuLib\MenuItemRenderer;

/**
 *
 */
abstract class BaseMenuRenderer implements MenuRendererInterface {
	/**
	 * @var MenuItemRenderer\MenuItemRendererInterface
	 */
	protected $itemRenderer;

	/**
	 * @var array
	 */
	public $settings = array();

	/**
	 * @param MenuItemRenderer\MenuItemRendererInterface $itemRenderer
	 * @param array $settings
	 */
	protected function __construct(MenuItemRenderer\MenuItemRendererInterface $itemRenderer, $settings = array()) {
		$this->itemRenderer = $itemRenderer;

		$this->settings = array_merge($this->settings, $settings);
	}
}

?>