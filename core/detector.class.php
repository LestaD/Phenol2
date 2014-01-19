<?php

namespace Core;

/**
 * Detector
 * 
 * Класс для определения пакета загрузки
 * 
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 1.1
 * @access public
 */
final class Detector extends \System\EngineBlock
{
	public $default_package = 'www';
	private $search_path = '';
	private $load_package = false;
	private $request_uri;
	private $language = false;
	
	public function __construct( &$registry )
	{
		parent::__construct( $registry );
		$this->search_path = DIR_ROOT . 'package_%package%';
		$this->request_uri = $_SERVER['REQUEST_URI'];
		
		$this->language = $this->userLanguage();
		
	}
	
	
	/**
	 * Установка пакета для подгрузки
	 * 
	 * @param string $packagepath Путь к пакету приложения
	 * @return
	 */
	public function setPackage( $packagename )
	{
		$packagepath = str_replace('%package%', $packagename, $this->search_path);
		if ( file_exists( $packagepath ) && is_dir( $packagepath ) )
		{
			$this->default_package = $packagename;
			return true;
		} else return false;
	}
	
	
	/**
	 * Возвращает текущий полный субдомен
	 * Если запущен по основному домену будет возвращено FALSE
	 * 
	 * @return
	 */
	public function getCurrentSubdomain()
	{
		$host = $_SERVER['HTTP_HOST'];
		$domain = $this->registry->fconfig->Server['Domain'];
		if ( $host == $domain )
		{
			return false;
		}
		else
		{
			$host = str_replace('.'.$domain, '', $host);
			return $host;
		}
		
	}
	
		
	/**
	 * Указание папки поиска пакетов
	 * В пути указать шаблон %package% - имя пакета
	 * 
	 * @param mixed $path
	 * @return void
	 */
	public function searchPackagesIn( $path )
	{
		$this->search_path = $path;
	}
	
		
	/**
	 * Определение пакета по алиасу домена
	 * 
	 * @param string $domain - alias or real package
	 * @return string - package
	 */
	public function detectAlias($domain)
	{
		$uri = $_SERVER['HTTP_HOST'];
		$packagepath = str_replace('%package%', $domain, $this->search_path);
		if ( file_exists( $packagepath ) && is_dir( $packagepath ) ) {
			return $domain;
		} else {
			foreach ( $this->registry->fconfig->Aliases as $p_name=>$p_data )
			{
				if ( in_array($domain, $p_data) ) {
					return $p_name;
				}
			}
		}
	}
	
	
	
	/**
	 * Запуск выбранного пакета
	 * 
	 * @param bool $package
	 * @return void
	 */
	public function runPackage( $package = false )
	{
		if ( $package !== false ) {
			$packagepath = str_replace('%package%', $package, $this->search_path);
			$defaultpackage = $packagepath;
		} else {
			$packagepath = str_replace('%package%', $this->load_package, $this->search_path);
			$defaultpackage = str_replace('%package%', $this->default_package, $this->search_path);
		}
		$file = '';
		$classname = '';
		if ( file_exists( $packagepath . DS . 'package.php' ) ) {
			$file = $packagepath . DS . 'package.php';
			$classname = $package ?: $this->load_package;
		} elseif ( file_exists( $defaultpackage . DS . 'package.php' )  ) {
			$file = $defaultpackage . DS . 'package.php';
			$classname = $package ?: $this->default_package;
		} else {
			$this->registry->error->errorPackageLoad($package ?: $this->load_package, $package ?: $this->default_package);
		}
		
		include $file;
		
		define( 'DIR_PACKAGE',	$defaultpackage );
		
		include DIR_CORE . 'cache.class.php';
		include DIR_CORE . 'local.class.php';
		include DIR_CORE . 'view.class.php';
		
		$this->cache = new Cache($this->registry);
		$this->locale = new Locale($this->registry);
		$this->view = new View($this->registry);
		
		$class = createClassname($classname, 'Package');
		$this->registry->package = new $class($this->registry);
		$this->registry->package->ConfigLoad();
		$this->registry->package->onRun( $this->detector->getArguments() );
	}
	
		
	/**
	 * Запуск контроллера по стандартной схеме
	 * 
	 * @param mixed $arguments
	 * @return void
	 */
	public function runControllerScheme( array $arguments )
	{
		if ( count($arguments) == 0 )
		{
			$this->load->controller('common/default');
			$this->controller->fireAction('default');
		}
		elseif ( count($arguments) == 1 )
		{
			$this->load->controller($arguments[0].'/default');
			$this->controller->fireAction('default');
		}
		elseif ( count($arguments) == 2 )
		{
			$this->load->controller($arguments[0].'/'.$arguments[1]);
			$this->controller->fireAction('default');
		}
		elseif ( count($arguments) > 2 )
		{
			$this->load->controller($arguments[0].'/'.$arguments[1]);
			$this->controller->fireAction($arguments[2]);
		}
	}
	
		
	/**
	 * Возвращает список аргументов запроса разделенных / в адресе
	 * 
	 * @param integer $startat
	 * @return
	 */
	public function getArguments( $startat = 0 )
	{
		list($args, $get) = explode('?', $_SERVER['REQUEST_URI']);
		$args = explode('/', $args);
		$arguments = array();
		foreach( $args as $arg )
		{
			if ( $arg == '' || $arg == null ) continue;
			$arguments[] = $arg;
		}
		
		$this->request->arguments = array();
		if ( count($arguments) > 3 ) {
			$cc = count($arguments);
			for ( $i = 3; $i < $cc; $i++ ) $this->request->arguments[] = $arguments[$i];
		}
		
		return $arguments;
	}
	
		
	/**
	 * Был ли запрошен файл
	 * 
	 * @return
	 */
	public function isFileRequested()
	{
		
		$uri = explode('?', $this->request_uri, 2);
		
		//qr($uri);
		
		$info = pathinfo($uri[0]);
		
		return isset( $info['extension'] );
	}
	
	
		
	/**
	 * Попытка отдачи запрошенного файла
	 * 
	 * @return void
	 */
	public function getFileRequested()
	{
		$info = pathinfo($this->request_uri);
		$type = $info['extension'];
		$file = $info['dirname'] . DS . $info['basename'];
		
		// Путь к запущенному пакету
		$package = str_replace('%package%', $this->default_package, $this->search_path) . DS . "resource";
		
		$mime = '';
		$accept = false;
		// Разрешен ли выбранный тип файла для отдачи браузеру
		if ( isset( $type, $this->registry->fconfig->Files['Accepted'][$type] ) )
		{
			$accept = true;
			$mime = $this->registry->fconfig->Files['Accepted'][$type];
		}
		// Запрещенный тип файлов
		elseif ( isset( $type, $this->registry->fconfig->Files['Forbidden'][$type] ) )
		{
			$accept = false;
			$mime = -1;
		}
		// Выдача браузеру неизвестного системе типа файлов
		else
		{
			$accept = true;
			$mime = $this->registry->fconfig->Files['Default']['default'];
		}
		
		// Проверка наличия файла
		if ( is_file( $package . DS . $file ) && file_exists( $package . DS . $file ) && $accept ) {
			$code = file_get_contents($package . DS . $file);
			header('Content-Type: ' . $mime, true);
			
			$h304="HTTP/1.x 304 Not Modified";
			$match = ""; $since = ""; $varr = array(); $varrvis = array();
			if (array_key_exists("HTTP_HOST",$_ENV)) $varr =& $_ENV;
			if (array_key_exists("HTTP_HOST",$_SERVER)) $varr =& $_SERVER;
			if (isset($varr["HTTP_IF_NONE_MATCH"])) $match = $varr["HTTP_IF_NONE_MATCH"];
			$match = trim( strval($match) );
			if ( isset($varr["HTTP_IF_MODIFIED_SINCE"]) ) $since = $varr["HTTP_IF_MODIFIED_SINCE"];
			$since = explode(";",$since);
			$since = strtotime( trim($since[0] ));
			
			$etag = '"' . md5($code) . '"';
			header('ETag: ' . $etag);
			header('Cache-Control: public, max-age=3600');
			
			header("Accept-Ranges: bytes");
			header("Expires: ".gmdate("r")." GMT");
			header("Connection: Keep-Alive");
			header("Keep-Alive: timeout=5, max=100");
			header("Last-Modified: " . gmdate("r", filemtime( $package . DS . $file )) . " GMT");
			
			
			$since = (int)$since;
			$dat = (int)filemtime($package . DS . $file);
			
			if ($since == $dat) {
				if ( $match==$etag){
					$varrvis[0]=$h304;
					header($h304);
					header("Connection: Close");
					exit;
				}
			}
			else {
				header("Last-Modified: ".gmdate("r", $dat)." GMT");
			}
			
			if ( $type == "css" OR $type == "js" ) {
				ob_start();
				eval("?>$code<?php\r\n");
				$ss = ob_get_contents();
				ob_clean();
				echo $ss;
			} else {
				echo $code;
			}
		}
		else
		{
			$this->registry->error->errorFileAccess($file);
		}
	}
	
	
	/**
	 * Определения языков пользователя
	 * 
	 * @return
	 */
	public function userLanguage()
	{
		if ( !$this->language )
		{
			if ( ($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) )
			{
				if ( preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list) )
				{
					$this->language = array_combine($list[1], $list[2]);
					foreach( $this->language as $n => $v )
					{
						$this->language[$n] = $v ? $v : 1;
					}
					arsort( $this->language, SORT_NUMERIC );
				}
			}
		}
		
		return $this->language;
	}
	
	
		
	/**
	 * Подбор подходящего языка
	 * 
	 * @param mixed $aliases
	 * @param string $default
	 * @return
	 */
	public function LanguageMatchBest( $aliases, $default = "en" )
	{
		$languages = array();
		foreach ( $aliases as $lang => $alias )
		{
			if ( is_array( $alias ) )
			{
				foreach( $alias as $alias_lang )
				{
					$languages[strtolower($alias_lang)] = strtolower($lang);
				}
			}
			else
			{
				$languages[strtolower($alias)] = strtolower($lang);
			}
		}
		
		foreach ( $this->language as $l => $v )
		{
			$s = strtok($l, '-');
			if ( isset($languages[$s]) ) return $languages[$s];
		}
		
		return $default;
	}
}



