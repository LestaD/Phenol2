<?php

class ModelRoomLoad extends Model
{
	public function loadTest()
	{
		$a = $this->db->query('SELECT * FROM  `test` LIMIT 0 , 30');
		return $a;
	}
	
	
}


