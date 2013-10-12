<?php


class EngineBlock
{
	protected $registry;
	
	public function __construct( Registry &$reg )
	{
		$this->registry = $reg;
	}
	
	public function __get( $key )
	{
		return $this->registry->get( $key );
	}
	
	public function __set( $key, $value )
	{
		$this->registry->set( $key, $value );
	}
}