<?php
App::uses('MenuRenderer', 'Menu.Lib/MenuLib/MenuRenderer');


/**
 *
 */
abstract class BaseMenuRenderer implements MenuRenderer {
    /**
     * @var MenuItemRenderer
     */
    protected $itemRenderer;

    /**
     * @var array
     */
    public $settings = array();

    /**
     * @param MenuItemRenderer $itemRenderer
     * @param array $settings
     */
    protected function __construct(MenuItemRenderer $itemRenderer, $settings = array()) {
        $this->itemRenderer = $itemRenderer;

        $this->settings = array_merge($this->settings, $settings);
    }

    /**
     * @abstract
     * @param Menu $menu
     * @return mixed
     */
    abstract function render(Menu $menu);

}
?>