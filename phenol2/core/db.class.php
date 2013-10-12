<?php


final class Database
{
	private $link;
	public $driver;
	
	public function __construct()
	{
		$this->link = null;
		$this->driver = null;
	}
	
	public function __call($method, $params)
	{
		if ( is_callable(array($this->driver, $method)) == true )
		{
			return call_user_func_array(array(&$this->driver, $method), $params);
		}
		return FALSE;
	}
	
	public function init( $drivername, $host, $user, $pass, $dbase, $encoding = false )
	{
		if ( file_exists(DIR_DRIVER . $drivername . '.php') && is_file( DIR_DRIVER . $drivername . '.php' ) )
		{
			include DIR_DRIVER . $drivername . '.php';
			$classname = $drivername;
			$classname{0} = strtoupper($classname{0});
			$classname = 'Driver'.$classname;
			$this->driver = new $classname($host, $user, $pass, $dbase);
			if ( $encoding !== false )
			{
				$this->driver->encoding($encoding);
			}
		}
		else
		{
			global $registry;
			$registry->error->errorDriverLoad($drivername);
		}
	}
}






