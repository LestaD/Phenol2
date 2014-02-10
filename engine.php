<?php
define( 'ENGINE',			'Phenol');
define( 'VERSION',			'2.0.9' );

define( 'DS',				'/' );
define( 'DIR_ENGINE',		dirname(__FILE__) . DS );
define( 'DIR_ROOT',			$_SERVER['DOCUMENT_ROOT'] . DS );
define( 'DIR_CORE',			DIR_ENGINE . 'core' . DS );
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

$phenol = new Core\Registry;

// 
$phenol->error = new Core\Phenol2ErrorListener($phenol);

// Все массивы с результатами запроса находятся в этом объекте
$phenol->request = new Core\Request();

// Конфигурация пакета
$phenol->config = new Core\Config();

// Парсинг системных настроек
$phenol->fconfig = (object)\Toml\Parser2::fromFile(DIR_ROOT . 'config.toml');

// Детектор пакетов
$phenol->detector = new Core\Detector($phenol);

// Для дополнительных контроллеров
$phenol->subctr = new Core\Registry;

// Загрузчик моделей, контроллеров
include DIR_CORE . 'loader.class.php';
$phenol->load = new Core\Loader($phenol);

// 
include DIR_CORE . 'crypt.class.php';
$phenol->crypt = new Core\Crypt;

// Объект для работы с базой данных
include DIR_CORE . 'db.class.php';
$phenol->db = new Core\Database( $phenol );


class Ph {
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

