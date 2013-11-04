<?php

class ModelCommonBase extends Model
{
	public function GetConfig()
	{
		if ( ($data = $this->cache->Cache("social-settings")) == false )
		{
			$q = $this->db->query("SELECT * FROM `(prefix)settings`");
			if ( $q->num_rows )
			{
				$data = array();
				foreach ( $q->rows as $row )
				{
					$data[$row['key']] = $row['value'];
				}
			}
			$this->cache->Write("social-settings", $data);
		}
		return $data;
	}
}



