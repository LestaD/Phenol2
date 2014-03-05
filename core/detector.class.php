<?php

/**
 * Detector
 * 
 * Класс для определения пакета загрузки
 * 
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 1.4
 * @access public
 */
final class Detector extends EngineBlock
{
	public $package_name = 'application'; // default_package
	public $request_uri = '/';
	public $baseuri = '';
	private $search_path = '';
	private $language = false;
	private $subdomain = false;
	private $aliases;
	
	public function __construct( &$registry )
	{
		parent::__construct( $registry );
		
		$this->aliases = array();
		
		$uri = explode("#", $_SERVER['REQUEST_URI']);
		
		$this->request_uri = $uri[0];
		
		$this->language = $this->userLanguage();
		
		// Выстраивание всех алиасов в единый список
		if ( isset( $this->registry->fconfig->Aliases ) ) {
			foreach ( $this->registry->fconfig->Aliases as $group => $alias ) {
				foreach ( $alias as $a ) {
					$this->aliases[$a] = $group;
				}
			}
		}
	}
	
	
	/**
	 * УСТАРЕЛО!!!
	 * Установка пакета для подгрузки
	 * 
	 * @param string $packagepath Путь к пакету приложения
	 * @return
	 */
	public function setPackage( $packagename )
	{
		$this->package_name = $packagename;
		define( 'DIR_PACKAGE',	$this->search_path . $packagename . DS );
	}
	
	
	/**
	 * Возвращает текущий полный субдомен
	 * Если запущен по основному домену будет возвращено FALSE
	 * 
	 * @return
	 */
	public function getCurrentSubdomain()
	{
		if ( $this->subdomain ) {
			return $this->subdomain;
		}
		
		$host = $_SERVER['HTTP_HOST'];
		$domain = $this->registry->fconfig->Server['Domain'];
		if ( $host == $domain )
		{
			return false;
		}
		else
		{
			return $this->subdomain = str_replace('.'.$domain, '', $host);
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
	 * @return string - package
	 */
	public function detectAlias( $configdomain )
	{
		$realdomain = $_SERVER['HTTP_HOST'];
		$requesturl = $_SERVER['REQUEST_URI'];
		$fulluri = $realdomain . $requesturl;
		$rpackage = FALSE;
		$ralias = '';
		
		foreach( $this->aliases as $alias=>$package )
		{
			$al = str_replace('*', $configdomain, $alias);
			$pos = stripos( $fulluri, $al );
			if ( $pos !== FALSE && $pos == 0 ) {
				$rpackage = $package;
				$ralias = $al;
				break;
			}
		}
		
		$result = (object)array();
		$result->package = $rpackage;
		$result->uri = str_replace($ralias, '', $fulluri);
		$result->domain = $ralias;
		
		return $result;
	}
	
	
	
	/**
	 * Запуск выбранного пакета
	 * 
	 * @param bool $package
	 * @return void
	 */
	public function runPackage()
	{
		$file = '';
		$classname = $this->package_name;
		if ( file_exists( $this->search_path . $this->package_name . DS . 'package.php' ) ) {
			$file = $this->search_path . $this->package_name . DS . 'package.php';
		} else {
			$this->registry->error->errorPackageLoad($this->package_name, $classname);
		}
		
		include $file;
		
		include DIR_CORE . 'cache.class.php';
		include DIR_CORE . 'local.class.php';
		include DIR_CORE . 'view.class.php';
		
		$this->cache = new Cache($this->registry);
		$this->locale = new Locale($this->registry);
		$this->view = new View($this->registry);
		$this->v = $this->view->v;
		
		$class = createClassname($classname, 'Package');
		$this->registry->package = new $class($this->registry);
		// $this->registry->package->ConfigLoad();
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
		$count = count($arguments);
		if ( $count == 0 )
		{
			$this->load->controller('common/default');
			$this->controller->fireAction('default');
		}
		elseif ( $count == 1 )
		{
			$this->load->controller($arguments[0].'/default');
			$this->controller->fireAction('default');
		}
		elseif ( $count == 2 )
		{
			$this->load->controller($arguments[0].'/'.$arguments[1]);
			$this->controller->fireAction('default');
		}
		elseif ( $count > 2 )
		{
			$this->load->controller($arguments[0].'/'.$arguments[1]);
			$this->controller->fireAction($arguments[2]);
		}
	}
	
	
		
	/**
	 * Контроллер должен иметь интерфейс UserService
	 * метод preAction() вызывается перед запуском любых Action
	 * 
	 * @return void
	 */
	public function dispatchPreActionEvents() {
		if ( is_subclass_of($this->registry->controller, "UserService") ) {
			$this->registry->controller->preAction();
		}
	}
	
		
	/**
	 * Контроллер должен иметь интерфейс UserService
	 * метод afterAction() вызывается перед самым рендерингом страницы
	 * 
	 * @return void
	 */
	public function dispatchAfterActionEvents() {
		if ( is_subclass_of($this->registry->controller, "UserService") ) {
			$this->registry->controller->afterAction();
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
		list($args, $get) = explode('?', $this->request_uri);
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
		$package = $this->search_path . $this->package_name . DS . "resource";
		
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
			
			// Непонятная херня
			$h304="HTTP/1.x 304 Not Modified";
			$match = ""; $since = ""; $varr = array(); $varrvis = array();
			if (array_key_exists("HTTP_HOST",$_ENV)) $varr =& $_ENV;
			if (array_key_exists("HTTP_HOST",$_SERVER)) $varr =& $_SERVER;
			if (isset($varr["HTTP_IF_NONE_MATCH"])) $match = $varr["HTTP_IF_NONE_MATCH"];
			$match = trim( strval($match) );
			if ( isset($varr["HTTP_IF_MODIFIED_SINCE"]) ) $since = $varr["HTTP_IF_MODIFIED_SINCE"];
			$since = explode(";",$since);
			$since = strtotime( trim($since[0] ));
			
			// Заголовочки
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
				
				include DIR_CORE .'viewbase.class.php';
				$v = new \Core\ViewBase($this->registry);
				$const = $v->constants();
				foreach ( $const as $c => $val )
				{
					$ss= str_replace( "{" . $c . "}", $val, $ss );
				}
				
				$code = $ss;
			}
			
			echo $code;
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

