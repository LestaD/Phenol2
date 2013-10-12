<?php

class Phenol2ErrorListener extends ErrorListener
{
	protected $registry;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
	}
	
	public function errorControllerLoad($controller)
	{
		qr('<b>Error:</b> Loading controller: ' . $controller);
		die();
	}
	
	
	public function errorControllerFireAction($controller, $action)
	{
		qr('<b>Error:</b> Fire action <i>' . $action . '</i> in controller: ' . $controller);
		die();
	}
	
	public function errorDriverLoad($driver)
	{
		qr('<b>Error:</b> Loading driver: ' . $driver);
		die();
	}
	
	public function errorPackageLoad($package,$default)
	{
		qr('<b>Error:</b> Loading package: <b>' . $package . '</b> (' . $default . ')' );
		die();
	}
	
	public function errorModelLoad($model)
	{
		qr('<b>Error:</b> Loading model: ' . $model);
		die();
	}
	
	public function errorTemplateRead($tpl)
	{
		qr('<b>Error:</b> Reading template: ' . $tpl);
		die();
	}
	
	public function errorTemplateLoad($tpl)
	{
		qr('<b>Error:</b> Loading template: ' . $tpl);
		die();
	}
}


