<?php
/**
 *
 */
interface MenuItemRenderer {
    /**
     * @abstract
     * @param MenuItem $item
     * @param MenuRenderer $childRenderer
     * @return mixed
     */
    function render(MenuItem $item, MenuRenderer $childRenderer = NULL);
}
?>