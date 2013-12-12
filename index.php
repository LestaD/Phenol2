<?php
if ( version_compare( PHP_VERSION, "5.4.5", "<" ) ) { exit( "PHP version must be higher than 5.4"); }
require 'phenol2/engine.php';

// Поиск пакетов приложений будет производиться в папке
$phenol->detector->searchPackagesIn(dirname(__FILE__).DS.'%package%'.DS);

$phenol->detector->default_package = "default";

// Определение текущего домена
$subdomain = $phenol->detector->getCurrentSubdomain();
if ( $subdomain ) {
	// Запуск пакета по имени домена
	$phenol->detector->setPackage($phenol->detector->detectAlias($subdomain));
}
else
{
	// Запуск стандартного пакета
	$phenol->detector->setPackage("lestad");
}


// Если был запрошен файл, а не адрес
if ( $phenol->detector->isFileRequested() ) {
	
	// Запрашиваем вывод файла или системную ошибку
	$phenol->detector->getFileRequested();
	die();
}


// Запуск выбранного пакета
$phenol->detector->runPackage();
