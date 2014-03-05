<?php


/**
 * Crypt
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 0.1
 * @access public
 */
class Crypt
{
	
		
	/**
	 * 
	 * 
	 * @param string $word
	 * @return string
	 */
	public function UserPassword ( $word )
	{
		return md5($word) . sha1($word);
	}
	
	
	
}

