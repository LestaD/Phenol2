<?php

class ModelCommonLogin extends Model
{
	
	public function FindUserByLoginOrEmail( $email )
	{
		$email = $this->db->escape($email);
		$q = $this->db->query("SELECT * FROM `(prefix)user` WHERE `email`='$email' OR `login`='$email'");
		if ( $q->num_rows )
		{
			return $q->row;
		}
		
		return false;
	}
	
	
}


