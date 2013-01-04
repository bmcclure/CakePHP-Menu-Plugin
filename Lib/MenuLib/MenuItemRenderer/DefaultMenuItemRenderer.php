<?php
namespace MenuLib\MenuItemRenderer;

use MenuLib\MenuRenderer;

/**
 *
 */
class DefaultMenuItemRenderer extends BaseMenuItemRenderer {
	/**
	 * @var \HtmlHelper
	 */
	protected $helper;

	/**
	 * @var array
	 */
	public $settings = array(
		'wrap' => '<li%s>%s</li>',
		'childrenWrap' => '%s',
		'childrenClass' => 'has-children',
		'activeClass' => 'active',
		'noLinkFormat' => '%s',
		'class' => '',
		'id' => '',
	);

	/**
	 * @param \Helper $helper
	 * @param MenuItemRendererInterface $itemRenderer
	 * @param array $settings
	 */
	public function __construct(\Helper $helper, $settings = array()) {
		$this->helper = $helper;

		$settings = array_merge($this->settings, $settings);

		parent::__construct($settings);
	}


	/**
	 * @param \MenuLib\MenuItem $item
	 * @param MenuRenderer\MenuRendererInterface $childRenderer
	 * @return string
	 */
	function render(\MenuLib\MenuItem $item, MenuRenderer\MenuRendererInterface $childRenderer = NULL) {
		$url = $item->getUrl();

		if (empty($url)) {
			$output = sprintf($this->settings['noLinkFormat'], $item->getTitle());
		} else {
			$output = $this->helper->link($item->getTitle(), $item->getUrl(), $item->getLinkOptions());
		}


		if ($item->hasChildren()) {
			$output .= $childRenderer->render($item->getChildren(), array('child' => true));
		}

		$class = $this->settings['class'] ? $this->settings['class'] . ' ' : '';
		if ($this->settings['childrenClass'] && $item->hasChildren()) {
			$class .= $this->settings['childrenClass'];
		}

		if ($item->isActive()) {
			if (!empty($class)) {
				$class .= ' ';
			}
			$class .= $this->settings['activeClass'];
		}

		$class = $class ? ' class="' . $class . '"' : '';
		$class .= $this->settings['id'] ? ' id="' . $this->settings['id'] . '"' : '';

		$output = sprintf($this->settings['wrap'], $class, $output);

		return $output;
	}

}

?>