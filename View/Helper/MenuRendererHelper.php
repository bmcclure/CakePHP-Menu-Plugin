<?php
App::uses('AppHelper', 'View/Helper');

use MenuLib\MenuRenderer;
use MenuLib\MenuItemRenderer;

/**
 * Outputs menus built with the MenuBuilderComponent
 */
class MenuRendererHelper extends AppHelper {
	/**
	 * Helper dependencies
	 *
	 * @var array
	 * @access public
	 */
	var $helpers = array('Html');

	/**
	 * Array of global menu
	 *
	 * @var array
	 * @access protected
	 */
	protected $_menus = array();

	/**
	 * @var array
	 */
	protected $_menuRenderers = array();

	/**
	 * @var array
	 */
	protected $_itemRenderers = array();

	/**
	 * Current user group
	 *
	 * @var String
	 * @access protected
	 */
	protected $_group = NULL;

	/**
	 * settings property
	 *
	 * @var array
	 * @access public
	 */
	public $settings = array(
		'helper' => 'Html',
		'menusVar' => 'menus',
		'authVar' => 'user',
		'authModel' => 'User',
		'authField' => 'group',
	);

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	function __construct(View $View, $settings = array()) {
		parent::__construct($View, (array) $settings);

		$this->settings = array_merge($this->settings, (array) $settings);

		if (isset($View->viewVars[$this->settings['menusVar']])) {
			$this->_menus = $View->viewVars[$this->settings['menusVar']];
		}

		//Kint::dump($this->_menus);

		if (isset($View->viewVars[$this->settings['authVar']]) &&
			isset($View->viewVars[$this->settings['authVar']][$this->settings['authModel']]) &&
			isset($View->viewVars[$this->settings['authVar']][$this->settings['authModel']][$this->settings['authField']])
		) {
			$this->_group = $View->viewVars[$this->settings['authVar']][$this->settings['authModel']][$this->settings['authField']];
		}

		$this->setDefaultRenderers();
	}

	/**
	 * @param null $helper
	 */
	protected function setDefaultRenderers($helper = NULL) {
		if ($helper == NULL) {
			$helper = $this->settings['helper'];
		}

		list($plugin, $helperName) = pluginSplit($helper);
		$helperObject = $this->loadHelper($helperName);

		$this->_itemRenderers['default'] = new MenuItemRenderer\DefaultMenuItemRenderer($helperObject);
		$this->_menuRenderers['default'] = new MenuRenderer\DefaultMenuRenderer($helperObject, $this->_itemRenderers['default']);
	}

	/**
	 * @param $name
	 * @return Helper
	 */
	protected function loadHelper($name) {
		list($plugin, $helperName) = pluginSplit($name);
		if (!isset($this->$helperName)) {
			$helper = $this->_View->loadHelper($name);
		}
		else {
			$helper = $this->$helperName;
		}

		return $helper;
	}

	/**
	 * @param $name
	 */
	public function render($name, $options = array()) {
		//Kint::dump($this->_menus);
		//Kint::trace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

		if (is_a($name, 'MenuLib\Menu')) {
			$menu = $name;
		}
		elseif (isset($this->_menus[$name])) {
			$menu = $this->_menus[$name];
		}
		else {
			return '';
		}

		return $this->renderMenu($menu, $options);
	}

	/**
	 * @param MenuLib\Menu $menu
	 */
	public function renderMenu(MenuLib\Menu $menu, $options = array()) {
		if (!is_a($menu, 'MenuLib\Menu')) {
			return '';
		}

		$rendererName = $menu->getRenderer();

		if (empty($this->_menuRenderers[$rendererName])) {
			return '';
		}

		/**
		 * @var MenuRenderer\MenuRendererInterface $renderer
		 */
		$renderer = $this->_menuRenderers[$rendererName];

		if (!is_a($renderer, 'MenuLib\MenuRenderer\MenuRendererInterface')) {
			return '';
		}

		return $renderer->render($menu, $options);
	}
}

?>