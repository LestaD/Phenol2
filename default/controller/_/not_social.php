<?php

class CDefaultNotSocial extends Controller
{
	public function ActionDefault()
	{
		//$udata = $this->api->getuserData();
		
		qr('Open social network!');
		qr('Fired `default` from `notsocial` with API: ' . $this->api->Version());
	}
}


