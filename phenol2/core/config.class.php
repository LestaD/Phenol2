<?php


class Config
{
	protected $config;
	
	public function __construct( $array = array() )
	{
		$this->config = $array;
	}
	
	public function append( $array )
	{
		$this->config = array_merge($this->config, $array);
	}
	
	public function update( $array )
	{
		$this->config = $array;
	}
	
	public function __get( $key )
	{
		return $this->config[$key];
	}
	
	public function __set( $key, $value )
	{
		$this->config[$key] = $value;
	}
}