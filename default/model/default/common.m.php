<?php


class ModelDefaultCommon extends Model
{
	public function TestRun()
	{
		if ( ($data = $this->cache->Cache("example")) == false )
		{
			$data = array('foo'=>'bar');
			$this->cache->Write("example", $data);
		}
		return $data;
	}
	
	
}