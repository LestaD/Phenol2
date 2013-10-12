<?php


abstract class ErrorListener
{
	
	abstract public function errorControllerLoad($controller);
	abstract public function errorControllerFireAction($controller, $action);
	abstract public function errorDriverLoad($driver);
	abstract public function errorPackageLoad($package,$default);
	abstract public function errorModelLoad($model);
	
	
	
} 






