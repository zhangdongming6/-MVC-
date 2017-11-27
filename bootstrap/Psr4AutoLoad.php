<?php
class Psr4AutoLoad
{
	public $namespaces = array();
	
	public function register()
	{
		spl_autoload_register(array($this , 'loadClass'));
	}
	
	public function loadClass($className)
	{
		$pos = strrpos($className , '\\');
		$namespace = substr($className , 0 , $pos+1);
		$realClassName = substr($className , $pos+1);
		
		$this->mapLoad($namespace , $realClassName);
	
	}
	public function addNamespace($namespace , $path)
	{
		
		$this->namespaces[$namespace][] = $path;
		
	}
	public function mapLoad($namespace , $realClassName)
	{

		if(isset($this->namespaces[$namespace]) == false) {
			
			$filename = str_replace('\\' , '/' , $namespace . $realClassName . '.php');
			
		} else {
			foreach ($this->namespaces[$namespace] as $path) {
				$filename = $path . '/' . $realClassName . '.php';
			}
		}
		$this->requireFile($filename);
	}
	
	public function requireFile($filename)
	{
		if (file_exists($filename)) {
			include $filename;
			return true;
		} 
		return false;
	}
}









