<?php
Class Session
	{
	private $db;
	
	function __construct(Mysqli $db)
		{
		$this->db = $db;
		
		if (!isset($_SESSION['krant']))
			{
			$_SESSION['krant'] = array();
			}
		}
	
	function __get($name)
		{
		return $_SESSION['krant'][$name];
		}
		
	function __set($name, $value)
		{
		$_SESSION['krant'][$name] = $value;
		}
	
	function del($name)
		{
		unset($_SESSION['krant'][$name]);
		}
	
	function logout()
		{
		$_SESSION['krant'] = array();
		}
	
	function login($info)
		{
		include 'config.php';
		if (isset($passwords[$info['role']]))
			{
			if ($info['password'] == $passwords[$info['role']])
				$pass = true;
			else
				$pass = false;
			}
		else
			{
			$pass = true;
			}
				
		if ($pass)
			{
			$this->username = $info['username' . $info['role']];
			$this->role = $info['role'];
			$this->logged = true;
			$this->log("login");
			}
		}
		
	function log($message) 
		{
		$this->db->query("INSERT INTO log(user, role, address, message) VALUES ('" . $this->username . "', " . $this->role . ", '" . $_SERVER['REMOTE_ADDR'] . "', '" . $message . "')");
		}
	}
?>