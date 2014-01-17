<?php

class CCommonDefault extends Controller
{
	public function ActionDefault()
	{
		
		$this->load->language('base');
		
		$this->view->generateBreadcrumbs(array(
			'HomePage' => '/',
			'PackageList' => false
		));
		
		$this->view->v->menu = array(
			'Add' => '/package/add',
			'Refresh' => '/'
		);
		
		$this->view->_title('Title');
		$this->view->template = 'body';
		$this->view->childs(array(
			'body' => 'home',
		));
		$this->view->render();
	}
}