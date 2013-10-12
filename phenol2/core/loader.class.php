<?php


final class Loader
{
	private $registry;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
	}
	
	
	public function controller( $path )
	{
		$fullpath = DIR_PACKAGE . 'controller' . DS . $path . '.php';
		
		if ( @file_exists($fullpath) && is_file($fullpath) )
		{
			$classname = createClassname($path, 'C');
			include $fullpath;
			$this->registry->controller = new $classname($this->registry);
		}
		else
		{
			$this->registry->error->errorControllerLoad($path);
		}
	}
	
	public function model( $path )
	{
		$fullpath = DIR_PACKAGE . 'model' . DS . $path . '.php';
		if ( @file_exists($fullpath) && is_file($fullpath) )
		{
			$classname = createClassname($path, 'Model');
			$path = str_replace(' ', '!', $path);
			$path = str_replace('_', '!', $path);
			$path = str_replace('/', '!', $path);
			$path = str_replace('\\', '!', $path);
			$path = 'model_' . str_replace('!', '_', $path);
			include $fullpath;
			$this->registry->set($path, new $classname($this->registry));
		}
		else
		{
			$this->registry->error->errorModelLoad($path);
		}
	}
	
	
}





