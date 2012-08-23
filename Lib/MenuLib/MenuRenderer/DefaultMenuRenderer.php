<?php
App::uses('BaseMenuRenderer', 'Menu.Lib/MenuLib/MenuRenderer');

/**
 *
 */
class DefaultMenuRenderer extends BaseMenuRenderer {
    /**
     * @var Helper
     */
    protected $helper;

    public $settings = array(
        'wrap' => '<nav%s>%s</nav>',
        'menuWrap' => '<ul%s>%s</ul>',
        'class' => 'menu',
        'id' => null,
        'itemSeparator' => null,
        'menuClass' => '',
        'menuId' => '',
        'evenOdd' => false,
        'firstLast' => false,
    );

    /**
     * @param Helper $helper
     * @param MenuItemRenderer $itemRenderer
     * @param array $settings
     */
    public function __construct(Helper $helper, MenuItemRenderer $itemRenderer, $settings = array()) {
        $this->helper = $helper;

        $settings = array_merge($this->settings, $settings);

        parent::__construct($itemRenderer, $settings);
    }

    /**
     * @param Menu $menu
     */
    function render(Menu $menu, $child = false) {
        $output = "";

        $items = $menu->getItems();
        $count = count($items);

        /**
         * @var MenuItem $item
         */
        $i = 1; // 1-based so that even/odd rows make more sense
        foreach ($items as $item) {
            $addClass = '';
            if ($this->settings['firstLast']) {
                if ($i == 1) {
                    $addClass .= 'first';
                } elseif ($i == $count) {
                    $addClass .= 'last';
                }
            }

            if ($this->settings['evenOdd']) {
                if ($addClass) {
                    $addClass .= ' ';
                }
                if ($i&1) {
                    $addClass .= 'odd';
                } else {
                    $addClass .= 'even';
                }
            }

            if (!empty($addClass)) {
                $class = $item->options['class'];
                if (empty($class)) {
                    $class = '';
                } else {
                    $class .= ' ';
                }
                $class .= $addClass;
                $item->options['class'] = $class;
            }

            $output .= $this->itemRenderer->render($item, $this);

            if (($i < $count - 1) && !empty($this->settings['itemSeparator'])) {
                $output .= $this->settings['itemSeparator'];
            }

            $i++;
        }

        $menuClass = $this->settings['menuClass'] ? ' class="'.$this->settings['menuClass'].'"' : '';
        $menuClass .= $this->settings['menuId'] ? ' id="'.$this->settings['menuId'].'"' : '';

        $output = sprintf($this->settings['menuWrap'], $menuClass, $output);

        if (!$child) {
            $parentClass = $this->settings['class'] ? ' class="'.$this->settings['class'].'"' : '';
            if ($this->settings['id'] != null) {
                $parentClass .= $this->settings['id'] ? ' id="'.$this->settings['id'].'"' : '';
            } else {
                $parentClass .= ' id="'.Inflector::slug($menu->name).'-menu"';
            }

            $output = sprintf($this->settings['wrap'], $parentClass, $output);
        }

        return $output;
    }

}
?>