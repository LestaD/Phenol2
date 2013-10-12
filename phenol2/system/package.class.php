<?php

class Package extends EngineBlock
{
	public function ConfigLoad()
	{
		include DIR_PACKAGE . 'config.php';
		$this->config->append($config);
		$this->config->usedb = true;
		$this->config->driver = 'mysqli';
		unset($config);
	}
	
}






