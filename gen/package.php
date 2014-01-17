<?php

class PackageGen extends \System\Package
{
	public function onRun($args)
	{
		$this->cache->Enable(DIR_PACKAGE);
		//$this->db->init();
		
		$lang = $this->detector->LanguageMatchBest(array(
			'ru'=>array('ru','uk')
		), 'en');
		
		
		$this->locale->folder = DIR_PACKAGE . 'i10n' . DS;
		$this->locale->setLanguage($lang);
		$this->locale->add('common');
		$this->view->folder = DIR_PACKAGE . 'view' . DS;
		
		$this->detector->runControllerScheme($args);
	}
}