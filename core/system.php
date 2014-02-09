<?php

// print_r;
function qr($a)
{
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}

// print_r  die
function qrd( $z )
{
	qr($z);
	die();
}

// var_dump
function vd( $a )
{
	var_dump($a);
}

// Check for exist
function chk()
{
	$num = func_num_args();
	$array = func_get_arg(0);
	$ret = true;
	for ( $i = 1; $i < $num; $i++ )
	{
		$z = func_get_arg($i);
		$ret = isset( $array[$z] ) && $ret;
	}
	return $ret;
}

// Check for empty or ''
function chke( $arr )
{
	$ret = true;
	if ( count($arr) < 1 ) { return false; }
	foreach ( $arr as $key=>$value )
	{
		$value = str_replace(' ', '', $value);
		$ret = ($value=='' OR $value == 'NULL' OR $value == false ) && $ret ? false : true; 
	}
	return $ret;
}


function clean( $var )
{
	return htmlspecialchars( stripslashes( $var ) );
}

// iChitat
function createClassname( $path, $add = 'Class' )
{
	$path = str_replace(' ', '!', $path);
	$path = str_replace('_', '!', $path);
	$path = str_replace('/', '!', $path);
	$path = str_replace('\\', '!', $path);
	$ls = explode('!', $path);
	$classname = $add;
	foreach($ls as $p)
	{
		$p{0} = strtoupper($p{0});
		$classname .= $p;
	}
	return $classname;
}


function is_assoc(array $array) {
	// Keys of the array
	$keys = array_keys($array);
	// If the array keys of the keys match the keys, then the array must
	// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
	return array_keys($keys) !== $keys;
}

function ParseTomlFile($file) {
	return (object)\Toml\Parser2::fromFile( $file );
}

function safefilerewrite($fileName, $dataToSave)
{    if ($fp = fopen($fileName, 'w'))
    {
        $startTime = microtime();
        do
        {            $canWrite = flock($fp, LOCK_EX);
           // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
           if(!$canWrite) usleep(round(rand(0, 100)*1000));
        } while ((!$canWrite)and((microtime()-$startTime) < 1000));
 
        //file was locked so now we can store information
        if ($canWrite)
        {            fwrite($fp, $dataToSave);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
 
}

function write_ini_file($array, $file)
{
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";
            foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
        }
        else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
    }
    safefilerewrite($file, implode("\r\n", $res));
}



