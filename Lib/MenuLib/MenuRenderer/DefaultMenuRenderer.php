<?php
namespace MenuLib\MenuRenderer;

use MenuLib\MenuItemRenderer;

/**
 *
 */
class DefaultMenuRenderer extends BaseMenuRenderer {
	/**
	 * @var \Helper
	 */
	protected $helper;

	public $settings = array(
		'wrap' => '<nav%s>%s</nav>',
		'menuWrap' => '<ul%s>%s</ul>',
		'class' => 'menu',
		'id' => NULL,
		'itemSeparator' => NULL,
		'menuClass' => '',
		'hideIfEmpty' => true,
		'menuId' => '',
		'evenOdd' => FALSE,
		'firstLast' => FALSE,
	);

	/**
	 * @param \Helper $helper
	 * @param MenuItemRenderer\MenuItemRendererInterface $itemRenderer
	 * @param array $settings
	 */
	public function __construct(\Helper $helper, MenuItemRenderer\MenuItemRendererInterface $itemRenderer, $settings = array()) {
		$this->helper = $helper;

		$settings = array_merge($this->settings, $settings);

		parent::__construct($itemRenderer, $settings);
	}

	/**
	 * @param \MenuLib\Menu $menu
	 * @param bool $child
	 * @return mixed|string
	 */
	function render(\MenuLib\Menu $menu, $options = array()) {
		$defaults = array(
			'child' => false,
			'wrap' => '%s',
		);

		$options = array_merge($defaults, $options);

		$output = "";

		$items = $menu->getItems();
		$count = count($items);

		if ($count == 0 && $this->settings['hideIfEmpty']) {
			return $output;
		}

		/**
		 * @var \MenuLib\MenuItem $item
		 */
		$i = 1; // 1-based so that even/odd rows make more sense
		foreach ($items as $item) {
			$addClass = '';
			if ($this->settings['firstLast']) {
				if ($i == 1) {
					$addClass .= 'first';
				}
				elseif ($i == $count) {
					$addClass .= 'last';
				}
			}

			if ($this->settings['evenOdd']) {
				if ($addClass) {
					$addClass .= ' ';
				}
				if ($i & 1) {
					$addClass .= 'odd';
				}
				else {
					$addClass .= 'even';
				}
			}

			if (!empty($addClass)) {
				$class = $item->options['class'];
				if (empty($class)) {
					$class = '';
				}
				else {
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

		$menuClass = $this->settings['menuClass'] ? ' class="' . $this->settings['menuClass'] . '"' : '';
		$menuClass .= $this->settings['menuId'] ? ' id="' . $this->settings['menuId'] . '"' : '';

		$output = sprintf($this->settings['menuWrap'], $menuClass, $output);

		if (!$options['child']) {
			$parentClass = $this->settings['class'] ? ' class="' . $this->settings['class'] . '"' : '';
			if ($this->settings['id'] != NULL) {
				$parentClass .= $this->settings['id'] ? ' id="' . $this->settings['id'] . '"' : '';
			}
			else {
				$parentClass .= ' id="' . \Inflector::slug($menu->name) . '-menu"';
			}

			$output = sprintf($this->settings['wrap'], $parentClass, $output);
		}

		$output = sprintf($options['wrap'], $output);

		return $output;
	}

}

?>