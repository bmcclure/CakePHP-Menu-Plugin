The readme is too old to use them for installing this CakePHP-Menu.
After hours of reading threads in the internet and reading the classes to install this Plugin correctly, i got it to work.

For beginners it is hard to understand what things are to do...
Here a little tutorial:

1. Download the newest package: https://github.com/bmcclure/CakePHP-Menu-Plugin/archive/cake-2.0.zip
ATTENTION: Not the old one which was linked in the readme!

2. Copy the whole folder "CakePHP-Menu-Plugin-cake-2.0" to your plugin directory (by default e.g. app/Plugin)

3. Rename the plugin folder to "Menu"

4. if you dont load all your plugins at once, you must load it in your bootstrap. write the following in your bootstrap (app/Config/bootstrap.php by default)
```php
CakePlugin::load('Menu', array('bootstrap' => true));
```

5. go to your AppController (by default app/Controller/AppController.php) and extend your components-variable or init the components variable:
```php
public $components = array('Menu.MenuBuilder');
```
So my components-array is now:
```php
public $components = array('DebugKit.Toolbar', 'Session', 'Menu.MenuBuilder');
```

6. create a new function in your AppController for creating the menu array (like this array in the readme. this works) and add the view-helper at the beginning:
```php
private function build_menu()
  {
		$this->helpers[] = 'Menu.MenuRenderer'; //Add the MenuHelper
                //Create the menu-Array
		$menus = array(
            array(
                'title' => 'User',
                'url' => array('controller' => 'users', 'action' => 'index'),
				'children' => array(
					array(
						'title' => 'User overview',
						'url' => array('controller' => 'users', 'action' => 'index'),
					),
					array(
						'title' => 'New User',
						'url' => array('controller' => 'users', 'action' => 'add'),
					),
				),
            ),
            array(
                'title' => 'Roles',
                'url' => array('controller' => 'roles', 'action' => 'index'),
				'children' => array(
					array(
						'title' => 'Role overview',
						'url' => array('controller' => 'roles', 'action' => 'index'),
					),
					array(
						'title' => 'New Role',
						'url' => array('controller' => 'roles', 'action' => 'add'),
					),
					array(
						'title' => 'Show Role-Restrictions',
						'url' => array('controller' => 'role_access_restrictions', 'action' => 'index'),
					),
					array(
						'title' => 'New Role-Restrictions',
						'url' => array('controller' => 'role_access_restrictions', 'action' => 'add'),
					),
					
				),
            ),
    );
	$this->MenuBuilder->setMenu('main-menu',$menus); //Now you set the name here! not in the array
    // For default settings name must be menus
//ATTENTION: default is now "menus", not "menu"
    $this->set('menus',$this->MenuBuilder->getMenu('main-menu')); //To have the menu in your View
	}
```
Use your new function in beforeFilter:
```php
	public function beforeFilter()
	{
		$this->build_menu();
	}
```
7. Render the menu in your view (e.g. default.ctp)
```php
<?php echo $this->MenuRenderer->render('main-menu'); ?>
```


Ready! now your menu were built ;-)
