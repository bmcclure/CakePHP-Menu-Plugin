<?php
$menuLibPath = CakePlugin::path('Menu') . 'Lib';
require_once $menuLibPath . DS . 'MenuLib' . DS . 'MenuLibClassLoader.php';
$menuLibClassLoader = new MenuLibClassLoader('MenuLib', $menuLibPath);
$menuLibClassLoader->register();
?>