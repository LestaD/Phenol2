<?php

class HelperCommonPage extends Helper
{
	public function onLoad()
	{
		$this->view->child('header', 'common/headerbase');
		$this->view->v->stylesheets = array();
		$this->view->v->stylesheets[] = "stylesheet.css";
		
		$this->view->footer = '';
	}
	
	
	// array('title', 'image', 'link')
	public function GenerateTopMenu( $array = array() )
	{
		$data = '';
		
		foreach( $array as $item )
		{
			$data .= '<a href="'.$item[2].'">'.$item[0].'</a>';
		}
		
		return $data;
	}
	
}


