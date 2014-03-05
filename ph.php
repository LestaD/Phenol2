<?php

defined('PH_DEBUG') or define('PH_DEBUG', true);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

class PH
{
	private $log;
	
	protected function __construct()
	{
		$this->log = new Log(PH2_DIR.'application.log');
	}
	
	
	public static function log()
	{
		
	}
	
	
	public static function qr($msg)
	{
		echo '<pre>';
		print_r($msg);
		echo '</pre>';
	}
	
	public static function error()
	{
		
	}
	
	public static function qrd($msg)
	{
		self::qr($msg);
		die();
	}
	
	
	
}

class CAutoload
{
	private $_classmap = false;
	private $_file = '';
	private static $_this = false;
	
	public static function __autoload( $classname )
	{
		if ( !self::$_this ) self::$_this = new self(); 
		self::$_this->load($classname);
	}
	
	private function __construct()
	{
		$this->_file = PH2_DIR . 'classmap.inc';
		if ( !$this->_classmap )
		{
			$this->loadClassMap();
		}
	}
	
	public function __destruct()
	{
		if ( PH_DEBUG ) {
			unlink($this->_file);
		}
	}
	
	private function load($classname)
	{
		if (isset($this->_classmap[$classname])) {
			require_once $this->_classmap[$classname]; 
		} else {
			
		}
	}
	
	private function loadClassMap()
	{
		if ( file_exists($this->_file) ) {
			$classmap = parse_ini_file($this->_file);
		}
		
		if ( !$classmap || count($classmap) < 1 ) {
			$classmap = $this->generateClassMap( $this->_file );
		}
		
		$this->_classmap = $classmap;
	}
	
	private function generateClassMap($file)
	{
		$classmap = '';
		$files = array_merge(
			glob(PH2_DIR . '*/*.php'),
			glob(PH2_DIR . '*/*/*.php'),
			glob(PH2_DIR . '*/*/*/*.php')
		);
		$count = count($files);
		
		for( $i = 0; $i < $count; $i++ )
		{
			$text = str_replace( array("\r\n", '{', "\t", '<?php', ';'), array(' ', ' ', ' ', '', ' '), file_get_contents( $files[$i] ) );
			$namespace = '';
			$start = strpos($text, 'namespace ', 0);
			if ( $start !== false ) {
				$start += 10;
				$end = strpos($text, ' ', $start);
				$namespace = substr($text, $start, $end-$start) . '\\';
				
			}
			
			$start = strpos($text, 'class ', 0);
			if ( $start === false ) continue;
			$start+=6;
			$end = strpos($text, ' ', $start);
			$classname = substr($text, $start, $end-$start);
			$classmap .= sprintf("%s = \"%s\"\r\n", $namespace.$classname, str_replace(PH2_DIR, '', $files[$i]));
		}
		file_put_contents($file, $classmap);
		return parse_ini_file($file);
	}
}

spl_autoload_register(array(CAutoload,'__autoload'));
