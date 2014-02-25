<?php
namespace Core;

/**
 * Database
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 0.5
 * @access public
 */
final class Database extends \System\EngineBlock
{
	private $link;
	public $driver;
	
	public function __construct( Registry &$registry )
	{
		$this->link = null;
		$this->driver = null;
		
		parent::__construct($registry);
	}
	
		
	/**
	 * Магический метод __call
	 * Автоматически вызывается на несуществем методе в текущем объекте
	 * 
	 * @param mixed $method
	 * @param mixed $params
	 * @return
	 */
	public function __call($method, $params)
	{
		if ( is_callable(array($this->driver, $method)) == true )
		{
			return call_user_func_array(array(&$this->driver, $method), $params);
		}
		return FALSE;
	}
	
		
	/**
	 * Инициализация подключение драйвера и соединения с базой данных
	 * 
	 * @param mixed $drivername
	 * @param mixed $host
	 * @param mixed $user
	 * @param mixed $pass
	 * @param mixed $dbase
	 * @param bool $encoding
	 * @return void
	 */
	public function init( $drivername = null, $host = null, $user = null, $pass = null, $dbase = null, $encoding = false )
	{
		$this->registry->package->ConfigLoad();
		
		if ( $driver !== null ) {
			return;
		}
		
		if ( $drivername == null ) {
			$drivername = $this->config->driver;
			$host = $this->config->db_host;
			$user = $this->config->db_user;
			$pass = $this->config->db_pass;
			$dbase = $this->config->db_base;
			$encoding = $encoding ?: $this->config->db_encode;
		}
		
		$classname = $drivername;
		$classname{0} = strtoupper($classname{0});
		$classname = 'Driver'.$classname;
		
		if ( !class_exists($classname) ) {
			if ( file_exists(DIR_DRIVER . $drivername . '.php') && is_file( DIR_DRIVER . $drivername . '.php' ) )
			{
				include DIR_DRIVER . $drivername . '.php';
				
				$this->driver = new $classname($host, $user, $pass, $dbase);
				if ( $encoding !== false )
				{
					$this->driver->encoding($encoding);
				}
			}
			else
			{
				$this->error->errorDriverLoad($drivername);
			}
		}
	}
	
	
	public function query( $sql )
	{
		$sql = str_replace("(prefix)", $this->config->db_prefix, $sql);
		
		return $this->driver->query($sql);
	}
}






