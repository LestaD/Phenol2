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
	
	public function LoginUser( $emailogin, $password )
	{
		$emailogin = $this->db->escape($emailogin);
		$password = $this->crypt->UserPassword($password);
		
		$q = $this->query("SELECT * FROM `(prefix)user` WHERE (`login`='$emailogin' OR `email`='$emailogin') AND `password`='$password'");
		
		return $q->num_rows > 0 ? true : false;
		
	}
	
	
}


