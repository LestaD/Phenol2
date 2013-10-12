<?php


final class Request
{
	public $get = array();
	public $post = array();
	public $files = array();
	public $cookie = false;
	public $session = false;
	public $arguments = false;
	
	
	public function __construct()
	{
		session_start();
		$this->get = $_GET;
		$this->post = clean($_POST);
		$this->files = clean($_FILES);
		$this->cookie = clean($_COOKIE);
		$this->session = &$_SESSION;
	}
	
	
}




