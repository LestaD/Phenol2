<?php

define( 'DIR_PACKAGE',			dirname(__FILE__).DS );

class PackageSocial extends \System\Package
{
	public function onLoad()
	{
		$args = $this->request->arguments = $this->detector->getArguments();
		
		$this->cache->Enable(DIR_PACKAGE);
		
		$this->db->init(
			$this->config->driver,
			$this->config->db_host,
			$this->config->db_user,
			$this->config->db_pass,
			$this->config->db_base,
			$this->config->db_encode
		);
		
		$this->load->model("common/base");
		$this->fconfig->i = $this->model->common_base->getConfig();
		
		$this->fconfig->i['user_language'] = $this->detector->LanguageMatchBest(array(
			'ru' => array('ru', 'be', 'uk', 'ky', 'ab', 'mo', 'et', 'lv'),
		), $this->fconfig->i['default_language']);
		
		
		$this->locale->folder = DIR_PACKAGE . 'L10n' . DS;
		$this->locale->setLanguage($this->fconfig->i['user_language']);
		$this->locale->add('common');
		$this->view->folder = DIR_PACKAGE . 'view' . DS;
		
		/*
		if ( !isset( $this->request->session['user_id'] ) )
		{
			$this->load->controller("common/login");
			qr($this->controller->getName());
			$this->controller->fireAction("default");
			return;
		}
		*/
		
		
		if ( count($args) == 0 )
		{
			if ( !isset( $this->request->session['user_id'] ) )
			{
				$this->load->controller("common/login");
				$this->controller->fireAction("default");
				return;
			}
			
			$this->load->controller('common/default');
			$this->controller->fireAction('default');
		}
		elseif ( count($args) == 1 )
		{
			// Загрузка короткой ссылки
			$this->load->controller('common/shortlink');
			$this->controller->fireAction($args[0]);
		}
		elseif ( count($args) == 2 )
		{
			$this->load->controller($args[0].'/'.$args[1]);
			$this->controller->fireAction('default');
		}
		elseif ( count($args) > 2 )
		{
			$this->load->controller($args[0].'/'.$args[1]);
			$this->controller->fireAction($args[2]);
		}
	}
	
	public function myFunc()
	{
		qr("Example");
	}
}






