<?php
/**
 *
 */
interface MenuRenderer {
    /**
     * @abstract
     * @param Menu $menu
     * @return mixed
     */
    function render(Menu $menu);
}
?>