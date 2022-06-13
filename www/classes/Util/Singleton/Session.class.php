<?php
namespace Util\Singleton;

use Util\Config;

/**
 * Session wrapper.
 *
 * @property bool $logged_in
 * @property string $username
 * @property int $role
 */
class Session
{
	/** @var Session|null Singleton instance. */
	private static ?Session $instance = null;

	/** @var string PHP session variable namespace. */
	protected const SESSION_NAMESPACE = 'krant';

	/**
	 * Returns the singleton instance.
	 * @return Session
	 */
	public static function instance(): Session
	{
		if (self::$instance === null) {
			self::$instance = new Session();
		}
		return self::$instance;
	}

	/**
	 * Sets up the Session object.
	 */
    public function __construct()
	{
		if (!isset($_SESSION[self::SESSION_NAMESPACE])) {
			$_SESSION[self::SESSION_NAMESPACE] = array();
		}
    }

	/**
	 * Magic attribute getter.
	 *
	 * @param string $name
	 * @return mixed
	 */
	function __get(string $name): mixed {
		if (key_exists($name, $_SESSION[self::SESSION_NAMESPACE])) {
			return $_SESSION[self::SESSION_NAMESPACE][$name];
		}
		return null;
	}

	/**
	 * Magic attribute setter.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	function __set(string $name, mixed $value): void {
		$_SESSION[self::SESSION_NAMESPACE][$name] = $value;
	}

	/**
	 * Empties the session attributes.
	 *
	 * @return void
	 */
	function reset(): void {
		$_SESSION[self::SESSION_NAMESPACE] = array();
	}

	/**
	 * Try to log in if the required $_POST variables are available.
	 *
	 * @return void
	 */
	public function check_login(): void {
		if (!$this->logged_in && isset($_POST['role'])) {
            $role = (int) $_POST['role'];
            $username = $_POST['username'][$role];
			$this->login($role, $_POST['password'], $username);
		}
	}

    /**
     * Log in given a role, password and username.
     *
     * @param int $role
     * @param string $password
     * @param string $username
     * @return void
     */
	public function login(int $role, string $password, string $username): void {
		if (!isset(Config::PASSWORDS[$role]) || Config::PASSWORDS[$role] === $password) {
			$this->username = $username;
			$this->role = $role;
			$this->logged_in = true;
		}
	}
}
