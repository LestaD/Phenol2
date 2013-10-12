<?php

/**
 * Detector
 * 
 * Класс для определения пакета загрузки
 * 
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 1.0
 * @access public
 */
final class Detector
{
	private $default_package = 'www';
	private $search_path = '';
	private $load_package = false;
	private $registry;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
		$this->search_path = DIR_ROOT . 'package_%package%';
	}
	
	
	/**
	 * Добавление пути для подключения файлов
	 * 
	 * @param string $packagepath Путь к приложению
	 * @return
	 */
	public function setDefaultPackage( $packagename )
	{
		$packagepath = str_replace('%package%', $packagename, $this->search_path);
		if ( file_exists( $packagepath ) && is_dir( $packagepath ) )
		{
			$this->default_package = $packagename;
			return true;
		} else return false;
	}
	
	
	public function searchPackagesIn( $path )
	{
		$this->search_path = $path;
	}
	
	public function detectDomainPackage( $domain )
	{
		$uri = $_SERVER['HTTP_HOST'];
		$packagename = str_replace('.'.$domain, '', $uri);
		$packagepath = str_replace('%package%', $packagename, $this->search_path);
		$this->load_package = $packagename;
		if ( file_exists( $packagepath ) && is_dir( $packagepath ) )
		{
			return true;
		} else return false;
	}
	
	public function runPackage( $package = false )
	{
		if ( $package !== false ) {
			$packagepath = str_replace('%package%', $package, $this->search_path);
			$defaultpackage = $packagepath;
		} else {
			$packagepath = str_replace('%package%', $this->load_package, $this->search_path);
			$defaultpackage = str_replace('%package%', $this->default_package, $this->search_path);
		}
		$file = '';
		$classname = '';
		if ( file_exists( $packagepath . DS . 'package.php' ) ) {
			$file = $packagepath . DS . 'package.php';
			$classname = $package ?: $this->load_package;
		} elseif ( file_exists( $defaultpackage . DS . 'package.php' )  ) {
			$file = $defaultpackage . DS . 'package.php';
			$classname = $package ?: $this->default_package;
		} else {
			$this->registry->error->errorPackageLoad($package ?: $this->load_package, $package ?: $this->default_package);
		}
		
		include $file;
		$class = createClassname($classname, 'Package');
		$this->registry->package = new $class($this->registry);
		$this->registry->package->ConfigLoad();
		$this->registry->package->onLoad();
	}
	
	
	public function getArguments( $startat = 0 )
	{
		list($args, $get) = explode('?', $_SERVER['REQUEST_URI']);
		$args = explode('/', $args);
		$arguments = array();
		foreach( $args as $arg )
		{
			if ( $arg == '' || $arg == null ) continue;
			$arguments[] = $arg;
		}
		return $arguments;
	}
	
}



