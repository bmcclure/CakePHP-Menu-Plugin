<?php
namespace MenuLib;

use MenuLib\MenuItemRenderer;

// uses Menu
/**
 *
 */
class MenuItem {
	/**
	 * @var
	 */
	protected $title;

	protected $active = FALSE;

	/**
	 * @var array
	 */
	protected $url = array();

	/**
	 * @var string
	 */
	protected $renderer = 'default';

	/**
	 * @var Menu
	 */
	protected $children;

	/**
	 * @var array
	 */
	public $options = array(
		'partialMatch' => FALSE,
		'linkOptions' => array(),
	);

	/**
	 * @param $title
	 * @param array $url
	 * @param array $options
	 */
	public function __construct($title, $url = array(), $options = array()) {
		$this->title = $title;
		$this->url = $url;

		if (isset($options['children'])) {
			foreach ((array) $options['children'] as $child) {
				$this->addChild($child);
			}
			unset($options['children']);
		}

		$this->options = array_merge($this->options, $options);
	}

	/**
	 * @return bool
	 */
	public function hasChildren() {
		return (!empty($this->children));
	}

	/**
	 * @param MenuItem $item
	 * @param $index
	 * @return bool
	 */
	public function addChildItem(MenuItem $item, $index = -1) {
		if (!isset($this->children)) {
			$this->children = new Menu($this->title);
		}

		return $this->children->addItem($item, $index);
	}

	/**
	 * @param Menu $menu
	 */
	public function setChildren(Menu $menu) {
		$this->children = $menu;
	}

	/**
	 * @return Menu|null
	 */
	public function getChildren() {
		if ($this->hasChildren()) {
			return $this->children;
		}

		return NULL;
	}

	/**
	 * @param $title
	 * @param array $url
	 * @param array $options
	 * @param $index
	 * @return bool
	 */
	public function addChild($title, $url = array(), $options = array(), $index = -1) {
		if (is_a($title, 'MenuLib\MenuItem')) {
			return $this->addChildItem($title);
		}

		if (is_array($title)) {
			$options = $title;
			$title = $options['title'];
			$url = $options['url'];

			unset($options['title']);
			unset($options['url']);
		}

		return $this->addChildItem(new MenuItem($title, $url, $options), $index);
	}

	/**
	 * @param $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return array
	 */
	public function getUrl() {
		return $this->url;
	}

	public function getLinkOptions() {
		return $this->options['linkOptions'];
	}

	public function setLinkOptions(array $linkOptions) {
		$this->options['linkOptions'] = $linkOptions;
	}

	/**
	 * @param MenuItemRenderer\MenuItemRendererInterface $renderer
	 */
	public function setRenderer(MenuItemRenderer\MenuItemRendererInterface $renderer) {
		$this->renderer = $renderer;
	}

	/**
	 * @return string
	 */
	public function getRenderer() {
		return $this->renderer;
	}

	public function isActive() {
		return ($this->active);
	}

	public function setActive($active) {
		$this->active = ($active);
	}
}

?>