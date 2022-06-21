<?php
namespace Util\Singleton;

use Model\User;
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
     * @return bool Whether the user is logged in.
     */
    public function isLoggedIn(): bool
    {
        if (!key_exists('logged_in', $_SESSION[self::SESSION_NAMESPACE]) ||
            $_SESSION[self::SESSION_NAMESPACE]['logged_in'] !== true) {
            $this->check_login();
        }
        return key_exists('logged_in', $_SESSION[self::SESSION_NAMESPACE]) &&
            $_SESSION[self::SESSION_NAMESPACE]['logged_in'] === true;
    }

    /**
     * @return User|null The user object.
     */
    public function getUser(): ?User
    {
        return key_exists('user', $_SESSION[self::SESSION_NAMESPACE]) ?
            unserialize($_SESSION[self::SESSION_NAMESPACE]['user']) : null;
    }

    /**
     * @return int|null The user's role level.
     */
    public function getRole(): ?int
    {
        return key_exists('role', $_SESSION[self::SESSION_NAMESPACE]) ?
            $_SESSION[self::SESSION_NAMESPACE]['role'] : null;
    }

    /**
     * @param bool $logged_in Whether the user is logged in.
     * @return void
     */
    public function setLoggedIn(bool $logged_in): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['logged_in'] = $logged_in;
    }

    /**
     * @param User $user The user object.
     * @return void
     */
    public function setUser(User $user): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['user'] = serialize($user);
    }

    /**
     * @param int $role The user's role level.
     * @return void
     */
    public function setRole(int $role): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['role'] = $role;
    }

	/**
	 * Empties the session attributes.
	 *
	 * @return void
	 */
	function reset(): void
    {
		$_SESSION[self::SESSION_NAMESPACE] = array();
	}

	/**
	 * Try to log in if the required $_POST variables are available.
	 *
	 * @return void
	 */
	public function check_login(): void
    {
		if (isset($_POST['role'])) {
            $role = (int) $_POST['role'];
            $user_id = $_POST['user'][$role];
			$this->login($role, $_POST['password'], $user_id);
            unset($_POST['role']);
		}
	}

    /**
     * Log in given a role, password and username.
     *
     * @param int $role
     * @param string $password
     * @param int $user_id
     * @return void
     */
	public function login(int $role, string $password, int $user_id): void
    {
        $user = User::getById($user_id);
        if ($user === null) {
            ErrorHandler::instance()->addError('Gebruiker niet gevonden.');
        }
        if ($user->perm_level < $role) {
            ErrorHandler::instance()->addError('Deze gebruiker mag deze rol niet gebruiken.');
        }
		if (!isset(Config::PASSWORDS[$role]) || Config::PASSWORDS[$role] === $password) {
			$this->setUser(User::getById($user_id));
			$this->setRole($role);
			$this->setLoggedIn(true);
		} else {
            ErrorHandler::instance()->addError('Onjuist wachtwoord.');
        }
	}
}
