<?php
namespace Util\Singleton;

use Util\Config;

class Session
{
	private static ?Session $instance = null;

	public static function instance(): Session
	{
		if (self::$instance === null) {
			self::$instance = new Session();
		}
		return self::$instance;
	}

    public function __construct()
	{
		if (!isset($_SESSION['krant'])) {
			$_SESSION['krant'] = array();
		}
    }
	
	function __get(string $name): mixed {
		if (key_exists($name, $_SESSION['krant'])) {
			return $_SESSION['krant'][$name];
		}
		return null;
	}
		
	function __set($name, $value): void {
		$_SESSION['krant'][$name] = $value;
	}
	
	function reset(): void {
		$_SESSION['krant'] = array();
	}

	public function check_login(): void {
		if (isset($_POST['role'])) {
			$this->login((int) $_POST['role'], $_POST['password'], $_POST['username']);
		}
	}

	public function login(int $role, string $password, array $usernames): void {
		if (!isset(Config::PASSWORDS[$role]) || Config::PASSWORDS[$role] === $password) {
			$this->username = $usernames[$role];
			$this->role = $role;
			$this->logged_in = true;
		}
	}
}
?>
