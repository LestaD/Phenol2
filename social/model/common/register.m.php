<?php


class ModelCommonRegister extends Model
{
	public function FindUserByLoginOrEmail( $email, $login )
	{
		$email = $this->db->escape($email);
		$login = $this->db->escape($login);
		$q = $this->db->query("SELECT * FROM `(prefix)user` WHERE `email`='$email' OR `login`='$login'");
		if ( $q->num_rows )
		{
			return $q->row;
		}
		
		return false;
	}
	
	
	
}




