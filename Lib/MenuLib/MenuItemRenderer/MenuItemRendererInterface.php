<?php
namespace MenuLib\MenuItemRenderer;

use MenuLib\MenuRenderer;

/**
 *
 */
interface MenuItemRendererInterface {
	/**
	 * @abstract
	 * @param \MenuLib\MenuItem $item
	 * @param MenuRenderer\MenuRendererInterface $childRenderer
	 * @return mixed
	 */
	function render(\MenuLib\MenuItem $item, MenuRenderer\MenuRendererInterface $childRenderer = NULL);
}

?>