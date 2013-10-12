<?php

class CRoomLoad extends Controller
{
	public function ActionDefault()
	{
		$this->ActionLoad();
	}
	
	public function ActionLoad()
	{
		$this->view->template = 'room/base';
		
		$this->load->model('room/load');
		$result = $this->model_room_load->loadTest();
		
		$this->view->child('header', 'room/header');
		$this->view->body = '<pre>Example print with magic method __set</pre>';
		$this->view->body2 = '<code>And this too work</code><br/>';
		
		$this->view->childs(array(
			'footer'=>'room/footer',
			'footer2'=>'room/footer2'
		));
		
		$this->view->vars(array(
			'a1'=>$this->locale->translate('_my'),
			'a2'=>$this->locale->translate('_name'),
			'a3'=>'Phenol 2'
		));
		
		$this->view->render();
	}
	
	public function ActionUpdate()
	{
		qr('Fired `update` from `room/load`');
		qr($this->request->get);
	}
}







