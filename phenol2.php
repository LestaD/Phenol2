<?php

defined('PH2') or define('PH2', 'Phenol2.2');
defined('PH2_DIR') or define('PH2_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined( 'PH2_ROOT') or define( 'PH2_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR );
defined('PH2_DEBUG') or define('PH2_DEBUG', true);
defined('PH2_VERSION') or define('PH2_VERSION', '2.2.0');

require PH2_DIR . DIRECTORY_SEPARATOR . 'ph.php';

class Phenol2 extends PH
{
	private static $_app;
	
	protected function __construct()
	{
		parent::__construct();
	}
	
	public static function createApplication( ApplicationConfig $config )
	{
		PH::qr($config->aliases['bky']);
	}
	
	public static function getVersion()
	{
		return PH2_VERSION;
	}
}