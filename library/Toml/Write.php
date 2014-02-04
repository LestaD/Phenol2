<?php

namespace Toml;

class Writer
{
	private $glob;
	private $categories;
	
	private function __construct( $input )
	{
		$this->glob = array();
		$this->categories = array();
		
		foreach ( $input as $key=>$val ) {
			if ( is_array($val) || is_object($val) ) {
				if ( is_assoc( (array)$val ) ) {
					$this->loop($key, (array)$val);
				} else {
					$this->glob[$key] = $val;
				}
			} else {
				$this->glob[$key] = $val;
			}
		}
		
		
	}
	
	
	private function loop( $target, $data )
	{
		foreach ( $data as $key=>$val ) {
			if ( is_array($val) || is_object($val) ) {
				if ( is_assoc( (array)$val ) ) {
					$this->loop($target.'.'.$key, $val);
				} else {
					$this->categories[$target][$key] = $val;
				}
			} else {
				$this->categories[$target][$key] = $val;
			}
		}
	}
	
	
	public function generate() {
		$code = "\r\n";
		foreach ( $this->glob as $key=>$val ) {
			if ( is_array($val) ) {
				$code .= "$key = [";
				$c = count($val);
				for ( $i = 0; $i < $c; $i++ ) {
					$code .= is_string($val[$i]) ? '"'.$val[$i].'"' : $val[$i];
					if ( ($i+1) < $c ) {
						$code .= ', ';
					}
				}
				$code .= "]\r\n";
			} else {
				$code .= '' . $key . ' = '. (is_string($val) ? '"'.$val.'"' : $val) . "\r\n";
			}
		}
		
		$code .= "\r\n";
		
		foreach ( $this->categories as $key=>$data ) {
			$code .= '['.$key.']'."\r\n";
			foreach( $data as $k=>$v ) {
				if ( is_array($v) ) {
					$code .= "$k = [";
					$c = count($v);
					for ( $i = 0; $i < $c; $i++ ) {
						$code .= is_string($v[$i]) ? '"'.$v[$i].'"' : $v[$i];
						if ( ($i+1) < $c ) {
							$code .= ',';
						}
					}
					$code .= "]\r\n";
					} else {
						$code .= '' . $k . ' = '. (is_string($v) ? '"'.$v.'"' : $v) . "\r\n";
					}
			}
			
			$code .= "\r\n";
		}
		
		return $code;
	}
	
	private function save( $file, $rewrite = true )
	{
		if ( file_exists($file) && !$rewrite ) {
			return false;
		}
		
		return file_put_contents($file, $this->generate()) ? true : false;
	}
	
	
	public static function saveToFile( $data, $filepath, $rewrite = true )
	{
		$p = new self($data);
		$p->save( $filepath, $rewrite );
	}
	
	public static function saveToVar( $data, &$variable ) {
		$p = new self($data);
		$variable = $p->generate();
	}
}


