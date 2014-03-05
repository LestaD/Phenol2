<?php


class ApplicationConfig
{
	var $domain;
	var $aliasOnly;
	var $packageDefault;
	var $aliases;
	
	var $cacheFolder;
	var $cacheLong;
	var $cacheDefault;
	var $cacheQuick;
	
	var $filesAccept;
	var $filesForbidden;
	var $filesDefault;
	
	var $appName;
	var $appVersion;
	
	public function __construct(array $config)
	{
		$this->appName = $config['name'];
		$this->appVersion = $config['version'];
		
		$this->domain = $config['Server']['Domain'];
		$this->aliasOnly = ($config['Server']['AliasOnly'] == 'true') ?: false;
		$this->packageDefault = (isset($config['Server']['AliasOnly'])) ? $config['Server']['AliasOnly'] : 'default';
		$this->aliases = isset($config['Aliases']) ? $config['Aliases'] : array();
		
		$this->filesAccept = isset($config['Files']['Accepted']) ? $config['Files']['Accepted'] : array();
		$this->filesForbidden = isset($config['Files']['Forbidden']) ? $config['Files']['Forbidden'] : array();
		$this->filesDefault = isset($config['Files']['Default']['default']) ? $config['Files']['Accepted']['default'] : 'application/octet-stream';
		
		$this->cacheDefault = isset($config['Cache']['Default']) ? (int)$config['Cache']['Default'] : 3600;
		$this->cacheLong = isset($config['Cache']['Long']) ? (int)$config['Cache']['Long'] : 86400;
		$this->cacheQuick = isset($config['Cache']['Quick']) ? $config['Cache']['Quick'] : 60;
		$this->cacheFolder = isset($config['Cache']['Folder']) ? $config['Cache']['Folder'] : 'cache';
	}
	
	public static function fromTomlFile( $tomlFile )
	{
		if ( file_exists($tomlFile))
		{
			return \Toml\Parser::parseFile($tomlFile);
		}
		else
		{
			PH::qrd(sprintf('<b>Error:</b> toml file not found "%s"', $tomlFile));
		}
	}
}




