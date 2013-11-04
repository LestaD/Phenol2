<?php

class CCommonRegister extends Controller
{
	public function ActionDefault()
	{
		$this->load->model("common/register");
		$this->load->language("common/register");
		$this->load->helper("common/page");
		
		$this->view->tr('title', 'title');
		
		// Пользователь уже вошел
		if ( isset( $this->request->session['user_id'] ) )
		{
			$this->redirect("/");
		}
		
		$this->view->errorbox = isset( $this->request->session['register_error'] ) ? $this->locale->translate($this->request->session['register_error']) : false;
		unset($this->request->session['register_error']);
		
		$this->view->v->topmenu = $this->helper->common_page->GenerateTopMenu(array(
			array($this->locale->translate('menu_homepage'), false, '/'),
			array($this->locale->translate('menu_login'), false, '/common/login')
		));
		
		if ( isset( $this->request->session['register'] ) )
		{
			$this->view->v->regdata_email = $this->request->session['register']['email'];
			$this->view->v->regdata_login = $this->request->session['register']['login'];
			unset( $this->request->session['register'] );
		}
		
		
		$this->view->v->action = "/common/register/commit";
		$this->view->v->step = 1;
		
		$this->view->child("content", "common/register_form");
		$this->view->template = "common/basepage";
		$this->view->render();
	}
	
	
	public function ActionCommit()
	{
		$this->load->model("common/register");
		if ( isset( $this->request->session['user_id'] ) )
		{
			$this->redirect("/");
		}
		
		
		
		if ( isset( $this->request->post['data'] ) && chk($this->request->post['data'], "email", "login") )
		{
			$data = array();
			foreach ( $this->request->post['data'] as $key=>$value )
			{
				$data[$key] = trim($this->db->escape(clean($value)));
			}
			
			$this->request->session['register'] = $data;
			
			if ( chke( $data ) )
			{
				if ( !filter_var($data['email'], FILTER_VALIDATE_EMAIL) )
				{
					$this->request->session['register_error'] = 'error_reg_not_email';
					$this->redirect('/common/register');
					return;
				}
				
				if ( $this->model->common_register->FindUserByLoginOrEmail($data['email'], $data['login']) )
				{
					$this->request->session['register_error'] = 'error_reg_exists';
					$this->redirect('/common/register');
					return;
				}
				
				
				$this->request->session['register'] = $data;
				$this->redirect('/common/register/step2');
			}
			else
			{
				$this->request->session['register_error'] = 'error_reg_emp';
				$this->redirect('/common/register');
			}
		}
		else
		{
			$this->request->session['register_error'] = 'error_reg_emp';
			$this->redirect('/common/register');
		}
		
		
	}
	
	public function ActionCommit2()
	{
		
	}
	
	
	
	public function ActionStep2()
	{
		$this->load->model("common/register");
		$this->load->language("common/register");
		$this->load->helper("common/page");
		
		$this->view->tr('title', 'title');
		
		// Пользователь уже вошел
		if ( isset( $this->request->session['user_id'] ) )
		{
			$this->redirect("/");
			return;
		}
		
		$this->view->errorbox = isset( $this->request->session['register_error'] ) ? $this->locale->translate($this->request->session['register_error']) : false;
		unset($this->request->session['register_error']);
		
		if ( !isset( $this->request->session['register'] ) )
		{
			$this->redirect("/common/register");
			return;
		}
		
		$this->view->v->topmenu = $this->helper->common_page->GenerateTopMenu(array(
			array($this->locale->translate('menu_homepage'), false, '/'),
			array($this->locale->translate('menu_login'), false, '/common/login')
		));
		
		$this->view->v->action = "/common/register/commit2";
		$this->view->v->step = 2;
		
		$this->view->v->user_email = $this->request->session['register']['email'];
		$this->view->v->user_login = $this->request->session['register']['login'];
		
		$this->view->child("content", "common/register_form");
		$this->view->template = "common/basepage";
		$this->view->render();
		
	}
}



