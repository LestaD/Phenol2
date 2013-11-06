<?php

namespace Core;

class Crypt
{
	
	
	public function UserPassword ( $word )
	{
		return md5($word) . sha1($word);
	}
	
	
	
}

