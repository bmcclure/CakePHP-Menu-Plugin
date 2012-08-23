<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Menu', 'Menu.Lib/MenuLib');
App::uses('MenuItem', 'Menu.Lib/MenuLib');
App::uses('DefaultMenuRenderer', 'Menu.Lib/MenuLib/MenuRenderer');
App::uses('DefaultMenuItemRenderer', 'Menu.Lib/MenuLib/MenuItemRenderer');

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
    var $helpers = array('Html', 'Kint.Kint');

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
        parent::__construct($View, (array)$settings);

        $this->settings = array_merge($this->settings, (array) $settings);

        if (isset($View->viewVars[$this->settings['menusVar']])) {
            $this->_menus = $View->viewVars[$this->settings['menusVar']];
        }

        //Kint::dump($this->_menus);

        if (isset($View->viewVars[$this->settings['authVar']]) &&
			isset($View->viewVars[$this->settings['authVar']][$this->settings['authModel']]) &&
			isset($View->viewVars[$this->settings['authVar']][$this->settings['authModel']][$this->settings['authField']])) {
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

        $itemRenderer = new DefaultMenuItemRenderer($helperObject);

        $this->_itemRenderers['default'] = $itemRenderer;
        $this->_menuRenderers['default'] = new DefaultMenuRenderer($helperObject, $itemRenderer);
    }

    /**
     * @param $name
     * @return Helper
     */
    protected function loadHelper($name) {
        list($plugin, $helperName) = pluginSplit($name);
        if (!isset($this->$helperName)) {
            $helper = $this->_View->loadHelper($name);
        } else {
            $helper = $this->$helperName;
        }

        return $helper;
    }

    /**
     * @param $name
     */
    public function render($name) {
        //Kint::dump($this->_menus);
        //Kint::trace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        if (is_a($name, 'Menu')) {
            $menu = $name;
        } elseif (isset($this->_menus[$name])) {
            $menu = $this->_menus[$name];
        } else {
            return '';
        }

        return $this->renderMenu($menu);
    }

    /**
     * @param Menu $menu
     */
    public function renderMenu(Menu $menu) {
        if (!is_a($menu, 'Menu')) {
            return '';
        }

        $rendererName = $menu->getRenderer();

        if (empty($this->_menuRenderers[$rendererName])) {
            return '';
        }

        /**
         * @var MenuRenderer $renderer
         */
        $renderer = $this->_menuRenderers[$rendererName];

        if (!is_a($renderer, 'MenuRenderer')) {
            return '';
        }

        return $renderer->render($menu);
    }
}
?>