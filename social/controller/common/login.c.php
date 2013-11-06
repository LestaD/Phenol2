<?php

class CCommonLogin extends Controller
{
	public function ActionDefault()
	{
		$this->load->model("common/login");
		$this->load->helper("common/page");
		$this->load->language("common/login");
		
		$this->view->tr('title', 'title');
		
		// Пользователь уже вошел
		if ( isset( $this->request->session['user_id'] ) )
		{
			$this->redirect("/");
		}
		
		$this->view->errorbox = isset( $this->request->session['login_error'] ) ? $this->locale->translate($this->request->session['login_error']) : false;
		unset($this->request->session['login_error']);
		
		$this->view->recovery = false;
		
		$this->view->v->topmenu = $this->helper->common_page->GenerateTopMenu(array(
			array($this->locale->translate('menu_homepage'), false, '/'),
			array($this->locale->translate('menu_register'), false, '/common/register')
		));
		
		
		if ( isset( $this->request->session['recovery'] ) ) {
			$this->view->recovery = true;
			unset($this->request->session['recovery']);
		}
		
		$this->view->action = "/common/login/check";
		
		
		$this->view->child("content", "common/login_form");
		$this->view->template = "common/basepage";
		$this->view->render();
	}
	
	
	
	public function ActionCheck()
	{
		$this->load->model("common/login");
		if ( isset( $this->request->session['user_id'] ) )
		{
			$this->redirect("/");
		}
		
		if ( isset( $this->request->post['data'] ) && chk( $this->request->post['data'], "login", "password" ) ) {
			$email = trim($this->db->escape(clean($this->request->post['data']['login'])));
			$password = trim($this->db->escape(clean($this->request->post['data']['password'])));
			
			if ( $email != "" && $password != "" ) {
				$user = $this->model->common_login->FindUserByLoginOrEmail($email);
				if ( !$user )
				{
					$this->request->session['login_error'] = 'error_eml_login_wrong';
					$this->request->session['recovery'] = true;
					$this->redirect('/common/login');
					return;
				}
				
				$auth = $this->model->common_login->LoginUser($email, $password);
				if ( !$auth )
				{
					$this->request->session['login_error'] = 'error_eml_passw_wrong';
					$this->request->session['recovery'] = true;
					$this->redirect('/common/login');
					return;
				}
				
				$this->request->session['user_id'] =  ( (int)$user['id'] );
				unset( $this->request->session['recovery'], $this->request->session['login_error'], $this->request->session['register'] );
				$this->redirect("/");
				return;
			}
			
			$this->request->session['login_error'] = 'error_eml_not_enter';
			$this->request->session['recovery'] = true;
			$this->redirect('/common/login');
		}
		else
		{
			$this->request->session['login_error'] = 'error_eml_not_enter';
			$this->redirect('/common/login');
		}
	}
	
	
	public function ActionLogout()
	{
		unset($this->request->session['user_id']);
		$this->redirect("/");
	}
	
}

