<?php
if ( version_compare( PHP_VERSION, "5.3.0", "<" ) ) { exit( "PHP version must be higher than 5.3"); }

define( 'ENGINE',		'Phenol');
define( 'VERSION',		'2.1.0' );

define( 'DS',			'/' );
define( 'DIR_ENGINE',		dirname(__FILE__) . DS );
define( 'DIR_ROOT',		$_SERVER['DOCUMENT_ROOT'] . DS );
define( 'DIR_CORE',		DIR_ENGINE . 'core' . DS );
define( 'DIR_SYSTEM',		DIR_ENGINE . 'system' . DS );
define( 'DIR_LIBRARY',		DIR_ENGINE . 'library' . DS );
define( 'DIR_DRIVER',		DIR_ENGINE . 'driver' . DS );
define( 'DIR_INTERFACE',	DIR_SYSTEM . 'interface' . DS );

include DIR_SYSTEM . 'error.class.php';
include DIR_SYSTEM . 'engineblock.class.php';
include DIR_SYSTEM . 'package.class.php';
include DIR_SYSTEM . 'controller.class.php';
include DIR_SYSTEM . 'model.class.php';
include DIR_SYSTEM . 'helper.class.php';

include DIR_CORE . 'system.php';
include DIR_CORE . 'errorlistener.class.php';
include DIR_CORE . 'registry.class.php';
include DIR_CORE . 'detector.class.php';
include DIR_CORE . 'request.class.php';
include DIR_CORE . 'config.class.php';

include DIR_LIBRARY . 'Toml/Parse.php';


class Ph {
	/**
	 * Запуск приложения с указанными настройками
	 * 
	 * @param mixed $config
	 * @return void
	 */
	static public function runApplication( $config )
	{
		$application = new self;
		$application->configurate(\Toml\Parser2::fromFile($config));
		$package = $application->detectPackage();
		$application->runPackage( $package );
	}
	
	private $phenol;
	
	
	/**
	 * Конструктор
	 * 
	 * @return void
	 */
	private function __construct()
	{
		$this->phenol = new Core\Registry;

		// 
		$this->phenol->error = new Core\Phenol2ErrorListener($this->phenol);
		
		// Все массивы с результатами запроса находятся в этом объекте
		$this->phenol->request = new Core\Request();
	}
	
	/**
	 * Конфигурация приложения
	 * 
	 * @param mixed $data
	 * @return void
	 */
	private function configurate( $data )
	{
		// Конфигурация пакета
		$this->phenol->config = new Core\Config();
		
		// Парсинг системных настроек
		$this->phenol->fconfig = (object)$data;
		
		// Детектор пакетов
		$this->phenol->detector = new Core\Detector($this->phenol);
		
		// Для дополнительных контроллеров
		$this->phenol->subctr = new Core\Registry;
		
		// Загрузчик моделей, контроллеров
		include DIR_CORE . 'loader.class.php';
		$this->phenol->load = new Core\Loader($this->phenol);
		
		// 
		include DIR_CORE . 'crypt.class.php';
		$this->phenol->crypt = new Core\Crypt;
		
		// Объект для работы с базой данных
		include DIR_CORE . 'db.class.php';
		$this->phenol->db = new Core\Database( $this->phenol );
	}
	
	/**
	 * Определение пакета
	 * 
	 * @return void
	 */
	private function detectPackage()
	{
		$this->checkConfig();
		$package = FALSE;
		$commondomain = $this->phenol->fconfig->Server['Domain'];
		$subdomain = $this->phenol->detector->getCurrentSubdomain();
		$aliasonly = strtolower($this->phenol->fconfig->Server['AliasOnly']) == "true" ? true : false;
		$defaultpackage = $this->phenol->fconfig->Server['DefaultPackage'];
		$aliaspackage = $this->phenol->detector->detectAlias($commondomain);
		
		// Если для адреса есть алиас
		if ( $aliaspackage->package )
		{
			$package = $aliaspackage->package;
			$this->phenol->detector->request_uri = $aliaspackage->uri;
			$this->phenol->detector->baseuri = '//'.$aliaspackage->domain;
		} else {
			// Если указана загрузка пакетов напрямую
			if ( !$aliasonly && $subdomain )
			{
				$package = $subdomain;
			} else {
				// Если загружается основной домен
				if ( $commondomain == $_SERVER['HTTP_HOST'] )
				{
					$package = $defaultpackage;
				}
			}
		}
		return $package;
	}
	
	/**
	 * Проверка корректности настроек
	 * 
	 * @return void
	 */
	private function checkConfig()
	{
		if ( !isset($this->phenol->fconfig->Server['Domain']) ) {
			qrd("ERROR!\r\n\r\n[Server]\r\nDomain = \"\"");
		}
		
		if ( !isset($this->phenol->fconfig->Server['AliasOnly']) ) {
			qrd("ERROR!\r\n\r\n[Server]\r\nAliasOnly = \"\"");
		}
		
		if ( !isset($this->phenol->fconfig->Server['DefaultPackage']) ) {
			qrd("ERROR!\r\n\r\n[Server]\r\nDefaultPackage = \"\"");
		}
	}
	
	/**
	 * Запуск определенного пакета
	 * 
	 * @param mixed $package
	 * @return void
	 */
	private function runPackage( $package )
	{
		$this->phenol->detector->searchPackagesIn(DIR_ROOT);
		$this->phenol->detector->setPackage($package);
		
		// Если был запрошен файл, а не адрес
		if ( $this->phenol->detector->isFileRequested() ) {
			
			// Запрашиваем вывод файла или системную ошибку
			$this->phenol->detector->getFileRequested();
			die();
		}
		
		$this->phenol->detector->runPackage();
	}
	
	
	// ======================================================================================
	
	private static $imported = array();
	
	/**
	 * Импорт библиотек и других системных файлов
	 * 
	 * @param mixed $path
	 * @return void
	 */
	static public function import( $path ) {
		
		if ( isset( self::$imported[$path] ) ) {
			return;
		}
		
		list($type, $data) = explode('.', $path, 2);
		
		$file = '';
		$error = '';
		switch($type) {
			case 'interface':
				$file = DIR_INTERFACE . $data . '.php';
				$error = 'interface file not found';
				break;
			
			case 'library':
				$file = DIR_LIBRARY . str_replace('.', '/', $data) . '.php';
				$error = 'library not found';
				break;
			
			case 'controller':
				$file = DIR_PACKAGE . 'controller/' . str_replace('.', '/', $data) . '.c.php';
				$error = 'controller not found';
				break;
			
			default:
				// Make error!!!
		}
		
		if ( file_exists($file) ) {
			require $file;
			self::$imported[$path] = $path;
		} else {
			die('<br><b>Fatal error</b>: '.$error.': <b>'.$path.'</b> <br>');
		}
	}
	
	
	static private function installExtModule( $file, $package ) {
		global $phenol;
		
		$extension = \Toml\Parser2::fromFile($file);
		$admin = base64_decode($extension['packages']['admin']);
		$application = base64_decode($extension['packages']['application']);
		$sql = base64_decode($extension['packages']['sql']);
		
		include (DIR_ROOT . $package.'/config.php');
		
		$phenol->db->init('mysqli', $config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'], $config['db_encode']);
		$phenol->db->query(str_replace('\r\n', '', str_replace('\t', '', $sql)));
		
		$time = time();
		
		if ( !is_dir( DIR_ROOT . 'temp/' ) ) {
			mkdir( DIR_ROOT . 'temp' );
		}
		
		$adminfile = DIR_ROOT.'temp/module_'.$extension['config']['name'].'_'.$time.'.admin.zip';
		file_put_contents($adminfile, $admin);
		
		
		$zip = new ZipArchive;
		$zip->open($adminfile);
		$zip->extractTo(DIR_ROOT . 'admin/');
		$zip->close();
		unlink($adminfile);
		
		$appfile = DIR_ROOT.'temp/module_'.$extension['config']['name'].'_'.$time.'.app.zip';
		file_put_contents($appfile, $application);
		
		$zip->open($appfile);
		$zip->extractTo(DIR_ROOT.$package.'/');
		$zip->close();
		unlink($appfile);
		
		
		header('Status: 302');
		header('Location: /');
	}
	
	
	static public function startInstallExtension()
	{
		global $phenol;
		if ( isset( $phenol->request->get['--phenol2-mode'], $phenol->request->get['--phenol2-name'] ) ) {
			switch ( $phenol->request->get['--phenol2-mode'] ) {
				case "module": {
					if ( file_exists( DIR_ROOT . $phenol->request->get['--phenol2-name'] )
						&& isset( $phenol->request->get['--install-application-name'] )
						&& file_exists( DIR_ROOT . $phenol->request->get['--install-application-name'] . '/package.php' ) ) {
						self::installExtModule(DIR_ROOT . $phenol->request->get['--phenol2-name'], $phenol->request->get['--install-application-name']);
					} else {
						qr("Package `".$phenol->request->get['--install-application-name']."` doesn't exists!");
					}
					break;
				}
				
				default:
					return;
			}
			
			exit();
		}
	}
}

if ( isset( $phenol->request->get['--install-phenol2-extension'] ) ) {
	Ph::startInstallExtension();
}

