<?php
namespace MenuLib\MenuRenderer;

/**
 *
 */
interface MenuRendererInterface {
	/**
	 * @abstract
	 * @param \MenuLib\Menu $menu
	 * @return mixed
	 */
	function render(\MenuLib\Menu $menu);
}

?>