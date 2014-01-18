<?php

class CCommonDefault extends Controller
{
	public function ActionDefault()
	{
		$this->load->model('default/common');
		
		$test = $this->model->default_common->TestRun();
		
		$this->view->v->foo = $test['foo'];
		
		$this->view->v->arguments = $this->request->arguments;
		$this->view->v->get = $this->request->get;
		
		$this->view->template = "body";
		$this->view->render();
	}
}