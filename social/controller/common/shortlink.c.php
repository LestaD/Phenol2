<?php


class CCommonShortlink extends Controller
{
	public function __call($name, $args)
	{
		switch( $name )
		{
			case "ActionLogout": {
				$this->load->controller("common/login");
				$this->controller->fireAction("logout");
				return;
			}
			
			
		}
	}
	
	
	
	
}





