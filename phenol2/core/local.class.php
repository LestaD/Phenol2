<?php


/**
 * ����� ��� ������ � ������������ �����
 * 
 * ������ ���� ��������� ����� Ini;
 * 
 * @package Phenol
 * @author LestaD
 * @copyright 2013
 * @version 1.3
 * @access public
 */
class Locale
{
	public $folder;
	
	private $keys;
	
	private $language;
	
	private $filelist;
	
	private $registry;
	
	/**
	 * �����������
	 * 
	 * @param mixed $lang - ���� �����������
	 * @return
	 */
	public function __construct( &$registry)
	{
		$this->language = 'english';
		$this->keys = array();
		$this->filelist = array();
		$this->folder = '';
	}
	
	
	
	/**
	 * ��������� ���� �����������
	 * 
	 * @param mixed $file - ��� ����� � ����� �����������
	 * @return
	 */
	public function add( $file )
	{
		$fullfile = $this->folder . $this->language . DS . $file . '.inc';
		
		if ( @file_exists($fullfile) )
		{
			$_ = &$this->keys;
			include $fullfile;
			$this->filelist[$file] = $file;
		}
	}
	
	
		
	/**
	 * Locale::addFullPath()
	 * 
	 * @param mixed $file
	 * @return void
	 */
	public function addFullPath( $file )
	{
		if ( @file_exists( $file ) )
		{
			$_ = &$this->keys;
			include $file;
			$this->filelist[$file] = $file;
		}
	}
	
	
	
	/**
	 * ��������� ������ �����
	 * 
	 * @param string $language - ���� � ������� "english", "russian"
	 * @return void
	 */
	public function setLanguage( $language = "english" )
	{
		$this->language = $language;
		$this->keys = array();
		foreach ( $this->filelist as $file )
		{
			$this->add($file);
		}
	}
	
	
	
	/**
	 * ���������� �������� �������� ����� �����������
	 * 
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}
	
	
		
	/**
	 * �������� ������ ���� ������
	 * 
	 * @return void
	 */
	static public function getAllLanguages()
	{
		$locale = new Locale();
		$list = array();
		
    	return $list;
	}
	
	
	/**
	 * ������� ���������� �����
	 * 
	 * @param mixed $word - ����� ��� ��������
	 * @param string $section - ������ ������ (Base)
	 * @return string
	 */
	public function get( $word )
	{
		return isset($this->keys[$word]) ? $this->keys[$word] : NULL;
	}
	
	
	
	/**
	 * ����� ���������� ����� � ������ �����������
	 * ���� ����� ������� ������������ ��� �������
	 * ���� ����� �� �������, �� ������������ ��������
	 * 
	 * @param string $word - ����� ��� ��������
	 * @param string $section - ������ ��� ������ �����
	 * @return string - ������� �����
	 */
	public function detect( $word, $section = "Base" )
	{
		return isset($this->keys[$word]) ? $this->keys[$word] : $word;
	}
	
	
		
	/**
	 * �������� ������� ������� Locale::translate()
	 * ������ ������ ������� �� ��������� - Base
	 * 
	 * @param string $word
	 * @return string
	 */
	public function translate( $word )
	{
		return $this->detect( $word );
	}
	
	
	
	/**
	 * ��������� ������ ���� ���� ������� �����������
	 * 
	 * @return array
	 */
	public function getAllArray()
	{
		return $this->keys;
	}

}







