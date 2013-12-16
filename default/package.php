<?php

if ( version_compare( VERSION, "2.0.3", "<" ) ) { qrd( "Old Phenol2 version! Package uses 2.0.3"); }

include "errorListener.php";

class PackageDefault extends \System\Package
{
	public function onRun( $args )
	{
		// Загрузка собственного обработчика ошибок
		$this->load->errorListener("DefaultErrors");
		
		// Активация кэширования
		$this->cache->Enable(DIR_PACKAGE);
		
		// Запуск базы данных по стандартным параметрам
		//$this->db->init();
		
		// Определение лучшего языка для пользователя
		$this->fconfig->i['user_language'] = $this->detector->LanguageMatchBest(array(
			'ru' => array('ru', 'be', 'uk', 'ky', 'ab', 'mo', 'et', 'lv'),
		), $this->fconfig->i['default_language']);
		
		// Директория с файлами перевода
		$this->locale->folder = DIR_PACKAGE . 'language' . DS;
		
		// Установка текущего языка
		$this->locale->setLanguage($this->fconfig->i['user_language']);
		
		// Подгрузка основного файла перевода
		$this->locale->add('common');
		
		// Директория шаблонов
		$this->view->folder = DIR_PACKAGE . 'view' . DS;
		
		// Запуск контроллера по стандартной схеме
		$this->detector->runControllerScheme( $args );
	}
}






