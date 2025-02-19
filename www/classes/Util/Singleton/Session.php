<?php

namespace Util\Singleton;

use Model\Log;
use Model\User;
use Util\Singleton\Configuration;

/**
 * Session wrapper.
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
        if (isset($_POST['filters'])) {
            $this->setFilter($_POST['filters']);
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
        if ($this->getUser() === null) {
            return false;
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
     * @return bool The user's gold value.
     */
    public function getGold(): bool
    {
        return key_exists('gold', $_SESSION[self::SESSION_NAMESPACE]) ?
            $_SESSION[self::SESSION_NAMESPACE]['gold'] : false;
    }

    /**
     * @return array array containing the filter
     */
    public function getFilter(): array
    {
        return key_exists('filter', $_SESSION[self::SESSION_NAMESPACE]) ?
           $_SESSION[self::SESSION_NAMESPACE]['filter'] : array();
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
     * @return void
     */
    public function setGold(): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['gold'] = rand(0, 500) == 5;
    }

    /**
     * @return void
     */
    public function setFilter(array $filters): void
    {
        $filters = array_diff($filters, [0]);
        $_SESSION[self::SESSION_NAMESPACE]['filter'] = $filters;
    }


    /**
     * Empties the session attributes.
     *
     * @return void
     */
    public function reset(): void
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
            $role = (int)$_POST['role'];
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
        if (!isset(Configuration::instance()->passwords[$role]) || Configuration::instance()->passwords[$role] === $password) {
            $this->setUser(User::getById($user_id));
            $this->setRole($role);
            $this->setLoggedIn(true);
            $this->setGold();
            Log::logInfo('Ingelogd.');
        } else {
            ErrorHandler::instance()->addError('Onjuist wachtwoord.');
        }
    }
}
