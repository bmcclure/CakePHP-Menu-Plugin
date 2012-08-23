<?php
App::uses('MenuItemRenderer', 'Menu.Lib/MenuLib/MenuItemRenderer');

/**
 *
 */
abstract class BaseMenuItemRenderer implements MenuItemRenderer {
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

    /**
     * @abstract
     * @param MenuItem $item
     * @param MenuRenderer $childRenderer
     * @return mixed
     */
    abstract function render(MenuItem $item, MenuRenderer $childRenderer = NULL);
}
?>