<?php
class Start
{
	static $loader;
	
	public static function run()
	{
		session_start();
		include 'bootstrap/Psr4AutoLoad.php';
		$namespace = include 'config/namespaces.php';
		//$config = include 'config/config.php';
		
		$GLOBALS['config'] = include 'config/config.php';
		
		self::$loader = new Psr4AutoLoad();
		self::$loader->register();
		self::addNamespaces($namespace);
		self::route();
	}
	
	public static function addNamespaces($namespace)
	{
		foreach ($namespace as $path => $namespace) {
			self::$loader->addNamespace($namespace , $path);
		}
	}
	
	public static function route()
	{
		$_GET['m'] = isset($_GET['m']) ? $_GET['m'] : 'Index';
		$_GET['a'] = isset($_GET['a']) ? $_GET['a'] : 'index';
		$_GET['m'] = ucfirst($_GET['m']);
		
		$controller = 'Controller\\' . $_GET['m'] . 'Controller';
		$c = new $controller();
		//echo $controller;
		//$_GEt['a']()
		
		call_user_func(array($c , $_GET['a']));
		
 	}
	
	
	
}
