<?php
App::uses('Component', 'Controller');

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
		'partialMatch' => FALSE,
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
		//$controller->View->{$this->settings['menusVar']} = $this->getMenus();
		$controller->set($this->settings['menusVar'], $this->getMenus());
	}

	/**
	 * @param $name
	 * @param array $items
	 * @param array $settings
	 */
	public function setMenu($name, $items = array(), $settings = array()) {
		if (is_a($name, 'MenuLib\Menu')) {
			$this->_menus[$name->name] = $name;
		}
		else {
			$settings = array_merge($this->defaultMenuSettings, $settings);
			$this->_menus[$name] = new MenuLib\Menu($name, $items, array_merge($this->defaultMenuSettings, $settings));
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
		if (!is_a($menu, 'MenuLib\Menu')) {
			$menu = $this->_menus[(string) $menu];
		}

		/**
		 * @var MenuLib\Menu $menu
		 */

		if (is_a($title, 'MenuLib\MenuItem')) {
			$menu->addItem($title);
		}
		else {
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
		 * @var MenuLib\Menu $menu
		 */
		foreach ($this->_menus as $name => $menu) {
			// Perhaps inject some logic here to decide which menus to process for this request

			/**
			 * @var MenuLib\MenuItem $item
			 */
			$this->setActiveItems($menu);

			$menus[$name] = $menu;
		}

		return $menus;
	}

	/**
	 * @param MenuLib\Menu $menu
	 * @return bool
	 */
	public function setActiveItems(MenuLib\Menu $menu) {
		$hasActiveItems = FALSE;

		foreach ($menu->getItems() as $item) {
			if ($this->setActive($item)) {
				$hasActiveItems = TRUE;
			}
		}

		return $hasActiveItems;
	}

	/**
	 * @param MenuLib\MenuItem $item
	 * @return bool
	 */
	public function setActive(MenuLib\MenuItem $item) {
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
	 * @param MenuLib\MenuItem $item
	 * @return bool
	 */
	public function itemIsActive(MenuLib\MenuItem $item) {
		if ($item->options['partialMatch']) {
			$check = (strpos(Router::normalize($this->_controller->request->url), Router::normalize($item->getUrl())) === 0);
		}
		else {
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
		$parent = new MenuLib\MenuItem($controllerName, array(
			'controller' => $underscoredName,
			'action' => 'index',
		), $this->defaultMenuItemOptions);

		foreach ($actions as $action) {
			$parent->addChild(Inflector::humanize($action), array(
				'controller' => $underscoredName,
				'action' => $action,
			), $this->defaultMenuItemOptions);
		}


		if (!is_a($menu, 'MenuLib\Menu')) {
			$menu = $this->_menus[(string) $menu];
		}

		/**
		 * @var MenuLib\Menu $menu
		 */
		$menu->addItem($parent, $startingIndex);

		return $startingIndex;
	}

	/**
	 * @param $controllerName
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