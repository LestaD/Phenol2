<?php
define( 'ENGINE',			'Phenol');
define( 'VERSION',			'2.0.7' );

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

// Загрузчик моделей, контроллеров
include DIR_CORE . 'loader.class.php';
$phenol->load = new Core\Loader($phenol);

// 
include DIR_CORE . 'crypt.class.php';
$phenol->crypt = new Core\Crypt;

// Объект для работы с базой данных
include DIR_CORE . 'db.class.php';
$phenol->db = new Core\Database( $phenol );


function ParseTomlFile($file) {
	return (object)\Toml\Parser2::fromFile( $file );
}

function safefilerewrite($fileName, $dataToSave)
{    if ($fp = fopen($fileName, 'w'))
    {
        $startTime = microtime();
        do
        {            $canWrite = flock($fp, LOCK_EX);
           // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
           if(!$canWrite) usleep(round(rand(0, 100)*1000));
        } while ((!$canWrite)and((microtime()-$startTime) < 1000));
 
        //file was locked so now we can store information
        if ($canWrite)
        {            fwrite($fp, $dataToSave);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
 
}

function write_ini_file($array, $file)
{
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";
            foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
        }
        else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
    }
    safefilerewrite($file, implode("\r\n", $res));
}


class Ph {
	
	
	/**
	 * Импорт библиотек и других системных файлов
	 * 
	 * @param mixed $path
	 * @return void
	 */
	static public function import( $path ) {
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
			
			default:
				// Make error!!!
		}
		
		if ( file_exists($file) ) {
			require $file;
		} else {
			die('<br><b>Fatal error</b>: '.$error.': <b>'.$path.'</b> <br>');
		}
	}
}

// is_subclass_of()




