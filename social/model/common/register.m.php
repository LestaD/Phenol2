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
	
	
	public function PreRegisterUser( $email, $login, $name, $surname, $password )
	{
		$email = $this->db->escape($email);
		$login = $this->db->escape($login);
		$name = $this->db->escape($name);
		$surname = $this->db->escape($surname);
		$password = $this->crypt->UserPassword($password);
		
		$regdate = time();
		
		$this->db->query("INSERT INTO `(prefix)user` (`login`,`email`,`password`,`name`,`surname`,`register_date`) VALUES ('$login', '$email', '$password', '$name', '$surname','$regdate')");
	}
}




