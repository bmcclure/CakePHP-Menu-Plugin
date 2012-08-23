<?php
App::uses('MenuItem', 'Menu.Lib/MenuLib');
/**
 *
 */
class Menu {
    /**
     * @var
     */
    public $name;

    /**
     * @var array
     */
    protected $items = array();

    /**
     * @var string
     */
    protected $renderer = 'default';

    /**
     * @var array
     */
    public $settings = array(

    );

    /**
     * @param $name
     * @param array $items
     * @param array $settings
     */
    public function __construct($name, $items = array(), $settings = array()) {
        $this->name = $name;

        $this->settings = array_merge($this->settings, (array) $settings);

        if (!empty($items)) {
            $this->addItems($items);
        }
    }

    /**
     * @param $items
     */
    public function addItems($items) {
        foreach ((array) $items as $item) {
            if (is_a($item, 'MenuItem')) {
                $this->addItem($item);
            } else {
                $this->add($item);
            }
        }
    }

    /**
     * @param MenuItem $item
     * @param $index
     * @return bool
     */
    public function addItem(MenuItem $item, $index = -1) {
        if ($index >= 0) {
            $this->items = array_splice($this->items, $index, 0, $item);
        } else {
            $this->items[] = $item;
        }

        return TRUE;
    }

    /**
     * @param $title
     * @param array $url
     * @param array $options
     * @param $index
     * @return bool
     */
    public function add($title, $url = array(), $options = array(), $index = -1) {
        if (is_a($title, 'MenuItem')) {
            return $this->addItem($title, $index);
        }
        if (is_array($title)) {
            $options = $title;

            $title = $options['title'];
            unset($options['title']);

            $url = $options['url'];
            unset($options['url']);
        }

        return $this->addItem(new MenuItem($title, $url, $options), $index);
    }

    /**
     * @return array
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param $renderer
     */
    public function setRenderer($renderer) {
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function getRenderer() {
        return $this->renderer;
    }
}
?>