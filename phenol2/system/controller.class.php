<?php

abstract class Controller extends EngineBlock
{
	
	public function fireAction( $action )
	{
		$target = createClassname($action, 'Action');
		if ( is_callable(array(&$this, $target)) )
		{
			call_user_func_array(array(&$this, $target),array());
		}
		else
		{
			$this->registry->error->errorControllerFireAction(get_class($this), $target);
		}
	}
	
	protected function redirect( $url, $status = 302 )
	{
		header('Status: ' . $status);
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
		exit();
	}
	
	
}


