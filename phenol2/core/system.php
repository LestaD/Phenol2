<?php

function qr($a)
{
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}

function vd( $a )
{
	var_dump($a);
}

function clean( &$var )
{
	if ( is_array( $var ) ) foreach( $var as $i=>$val ) clean($i);
	else $var = htmlspecialchars( stripslashes( $var ) );
}

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

