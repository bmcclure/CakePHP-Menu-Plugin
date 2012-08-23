<?php
App::uses('Component', 'Controller');
App::uses('Menu', 'Menu.Lib/MenuLib');
App::uses('MenuItem', 'Menu.Lib/MenuLib');

/**
 *
 */
class MenuBuilderComponent extends Component {
    /**
     * @var Controller
     */
    protected $_controller;

    /**
     * @var array
     */
    protected $_menus = array();

    /**
     * @var array
     */
    public $defaults = array(
        'menusVar' => 'menus',
    );

    /**
     * @var array
     */
    public $defaultMenuSettings = array();

    /**
     * @var array
     */
    public $defaultMenuItemOptions = array(
        'partialMatch' => false,
    );

	/**
	 * Constructor
	 *
	 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
	 * @param array $settings Array of configuration settings.
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->_controller = $collection->getController();

        foreach (array('defaultMenuSettings', 'defaultMenuItemOptions') as $setting) {
            if (isset($settings[$setting])) {
                $this->$setting = array_merge($this->$setting, $settings[$setting]);
                unset($settings[$setting]);
            }
        }

        $settings = array_merge($this->defaults, $settings);

		parent::__construct($collection, $settings);
	}

    /**
     * @param Controller $controller
     */
    public function beforeRender(Controller $controller) {
        $controller->set($this->settings['menusVar'], $this->getMenus());
    }

    /**
     * @param $name
     * @param array $items
     * @param array $settings
     */
    public function setMenu($name, $items = array(), $settings = array()) {
        if (is_a($name, 'Menu')) {
            $this->_menus[$name->name] = $name;
        } else {
            $settings = array_merge($this->defaultMenuSettings, $settings);
            $this->_menus[$name] = new Menu($name, $items, array_merge($this->defaultMenuSettings, $settings));
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMenu($name) {
        return $this->_menus[$name];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name) {
        return $this->getMenu($name);
    }

    /**
     * @param $name
     * @param array $items
     * @param array $settings
     */
    public function set($name, $items = array(), $settings = array()) {
        $this->setMenu($name, $items, $settings);
    }

    /**
     * @param $name
     * @param array $items
     * @param array $settings
     */
    public function menu($name, $items = array(), $settings = array()) {
        $this->setMenu($name, $items, $settings);
    }

    /**
     * @param $menu
     * @param $title
     * @param array $url
     * @param array $options
     */
    public function addItem($menu, $title, $url = array(), $options = array()) {
        if (!is_a($menu, 'Menu')) {
            $menu = $this->_menus[(string) $menu];
        }

        /**
         * @var Menu $menu
         */

        if (is_a($title, 'MenuItem')) {
            $menu->addItem($title);
        } else {
            $options = array_merge($this->defaultMenuItemOptions, $options);
            $menu->add($title, $url, array_merge($this->defaultMenuItemOptions, $options));
        }
    }

    /**
     * @param $menu
     * @param $title
     * @param array $url
     * @param array $options
     */
    public function item($menu, $title, $url = array(), $options = array()) {
        $this->addItem($menu, $title, $url, $options);
    }

    /**
     * @return array
     */
    public function getMenus() {
        $menus = array();

        /**
         * @var Menu $menu
         */
        foreach ($this->_menus as $name => $menu) {
            // Perhaps inject some logic here to decide which menus to process for this request

            /**
             * @var MenuItem $item
             */
            $this->setActiveItems($menu);

            $menus[$name] = $menu;
        }

        //debug($menus);
        //Kint::trace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        //Kint::dump($menus);

        return $menus;
    }

    /**
     * @param Menu $menu
     * @return bool
     */
    public function setActiveItems(Menu $menu) {
        $hasActiveItems = FALSE;

        foreach ($menu->getItems() as $item) {
            if ($this->setActive($item)) {
                $hasActiveItems = TRUE;
            }
        }

        return $hasActiveItems;
    }

    /**
     * @param MenuItem $item
     * @return bool
     */
    public function setActive(MenuItem $item) {
        $active = FALSE;

        if ($item->hasChildren()) {
            $active = $this->setActiveItems($item->getChildren());
        }

        if (!$active) {
            $active = $this->itemIsActive($item);
        }

        $item->setActive($active);
        return $active;
    }

    /**
     * @param MenuItem $item
     * @return bool
     */
    public function itemIsActive(MenuItem $item) {
        if($item->options['partialMatch']) {
            $check = (strpos(Router::normalize($this->_controller->request->url), Router::normalize($item->getUrl()))===0);
        } else {
            $check = Router::normalize($this->_controller->request->url) === Router::normalize($item->getUrl());
        }

        return $check;
    }

    /**
     * @param $menu
     * @param null $controller
     * @param array $actions
     * @param $startingIndex
     * @return mixed
     */
    public function controllerMenu($menu, $controller = NULL, $actions = array(), $startingIndex = -1) {
		if (is_null($controller)) {
            $index = $startingIndex;
			foreach (App::objects('Controller') as $controller) {
				$index = $this->controllerMenu($menu, $controller, $actions, $index + 1);
			}

            return $index;
		}

		if (is_null($actions)) {
			$actions = $this->_getControllerMethods($controller);
		}

        $controllerName = substr($controller, 0, -10);
        $underscoredName = Inflector::underscore($controllerName);
        $parent = new MenuItem($controllerName, array(
            'controller' => $underscoredName,
            'action' => 'index',
        ), $this->defaultMenuItemOptions);

        foreach ($actions as $action) {
            $parent->addChild(Inflector::humanize($action), array(
                'controller' => $underscoredName,
                'action' => $action,
            ), $this->defaultMenuItemOptions);
        }


        if (!is_a($menu, 'Menu')) {
            $menu = $this->_menus[(string) $menu];
        }

        /**
         * @var Menu $menu
         */
        $menu->addItem($parent, $startingIndex);

        return $startingIndex;
	}

    /**
     * @param $controllerName
     * @param $plugins
     * @return array
     */
    protected function _getControllerMethods($controllerName) {
        $classMethodsCleaned = array();
        $foundController = NULL;

        /**
         * @var Controller $controller
         */
        foreach (App::objects('Controller') as $controller) {
            if ($controllerName == $controller->name) {
                $foundController = $controller;
                break;
            }
        }

        if ($foundController != NULL) {
            $parentClassMethods = get_class_methods(get_parent_class($foundController));
            $subClassMethods = get_class_methods($foundController);
            $classMethods = array_diff($subClassMethods, $parentClassMethods);

            foreach ($classMethods as $method) {
                if ($method{0} == "_") {
                    continue;
                }

                $classMethodsCleaned[] = $method;
            }
        }

        return $classMethodsCleaned;
    }
}
?>