<?php

class Model extends \System\EngineBlock
{
	
	
	
	public function query( $sql )
	{
		return $this->db->query( $sql );
	}
	
	
	public function escape( $v )
	{
		return $this->db->escape($v);
	}
	
	
}








