<?php

namespace App\Util\Singleton;

use App\Model\Category;
use App\Model\Log;
use App\Model\User;

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
     */
    public static function instance(): Session
    {
        if (null === self::$instance) {
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
            $_SESSION[self::SESSION_NAMESPACE] = [];
        }
        if (isset($_POST['filters'])) {
            $this->setFilter($_POST['filters']);
        }
        if (isset($_GET['filter_mode'])) {
            $this->setFilterMode($_GET['filter_mode']);
        }
        if (isset($_GET['filter_categories'])) {
            $this->setFilterCategories($_GET['filter_categories']);
        }
    }

    /**
     * @return bool whether the user is logged in
     */
    public function isLoggedIn(): bool
    {
        if (!key_exists('logged_in', $_SESSION[self::SESSION_NAMESPACE])
            || true !== $_SESSION[self::SESSION_NAMESPACE]['logged_in']) {
            $this->check_login();
        }
        if (null === $this->getUser()) {
            return false;
        }

        return key_exists('logged_in', $_SESSION[self::SESSION_NAMESPACE])
            && true === $_SESSION[self::SESSION_NAMESPACE]['logged_in'];
    }

    /**
     * @return User|null the user object
     */
    public function getUser(): ?User
    {
        return key_exists('user', $_SESSION[self::SESSION_NAMESPACE]) ?
            unserialize($_SESSION[self::SESSION_NAMESPACE]['user']) : null;
    }

    /**
     * @return int|null the user's role level
     */
    public function getRole(): ?int
    {
        return key_exists('role', $_SESSION[self::SESSION_NAMESPACE]) ?
            $_SESSION[self::SESSION_NAMESPACE]['role'] : null;
    }

    /**
     * @return bool the user's gold value
     */
    public function getGold(): bool
    {
        return key_exists('gold', $_SESSION[self::SESSION_NAMESPACE]) ?
            $_SESSION[self::SESSION_NAMESPACE]['gold'] : false;
    }

    /**
     * @return int int representing how to filter
     * 0 - alle stukjes
     * 1 - alle stukjes die klaar zijn
     * 2 - alle stukjes die klaar zijn & nog niet nagekeken
     * 3 - alle stukjes die klaar zijn & nagekeken
     */
    public function getFilterMode(): int
    {
        return key_exists('filter_mode', $_SESSION[self::SESSION_NAMESPACE]) ?
           $_SESSION[self::SESSION_NAMESPACE]['filter_mode'] : 1;
    }

    /**
     * @return bool if the filter on categories is active
     */
    public function getFilterCategories(): bool
    {
        return key_exists('filter_categories', $_SESSION[self::SESSION_NAMESPACE]) ?
           $_SESSION[self::SESSION_NAMESPACE]['filter_categories'] : false;
    }

    /**
     * @return array<int, int> array containing the filter
     */
    public function getFilter(): array
    {
        return key_exists('filter', $_SESSION[self::SESSION_NAMESPACE]) ?
           $_SESSION[self::SESSION_NAMESPACE]['filter'] : array_map(static function ($cat) {return $cat->id; }, Category::getALL());
    }

    /**
     * @param bool $logged_in whether the user is logged in
     */
    public function setLoggedIn(bool $logged_in): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['logged_in'] = $logged_in;
    }

    /**
     * @param User $user the user object
     */
    public function setUser(User $user): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['user'] = serialize($user);
    }

    /**
     * @param int $role the user's role level
     */
    public function setRole(int $role): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['role'] = $role;
    }

    public function setGold(): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['gold'] = 5 == rand(0, 500);
    }

    /**
     * @param int $filter_mode Which filter is active
     * 0 - alle stukjes
     * 1 - alle stukjes die klaar zijn
     * 2 - alle stukjes die klaar zijn & nog niet nagekeken
     * 3 - alle stukjes die klaar zijn & nagekeken
     */
    public function setFilterMode(int $filter_mode): void
    {
        $filter_mode = $filter_mode <= 3 && $filter_mode >= 0 ? $filter_mode : 1;
        $_SESSION[self::SESSION_NAMESPACE]['filter_mode'] = $filter_mode;
    }

    /**
     * @param bool $filter_categories If the filter on categories is active
     */
    public function setFilterCategories(bool $filter_categories): void
    {
        $_SESSION[self::SESSION_NAMESPACE]['filter_categories'] = $filter_categories;
    }

    /**
     * @param array<int, int> $filters the categories that are shown
     */
    public function setFilter(array $filters): void
    {
        $filters = array_diff($filters, [0]);
        $_SESSION[self::SESSION_NAMESPACE]['filter'] = $filters;
    }

    /**
     * Empties the session attributes.
     */
    public function reset(): void
    {
        $_SESSION[self::SESSION_NAMESPACE] = [];
    }

    /**
     * Try to log in if the required $_POST variables are available.
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
     */
    public function login(int $role, string $password, int $user_id): void
    {
        $user = User::getById($user_id);
        if (null === $user) {
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
