<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\Category;
use Model\User;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Gebruikers pagina.
 */
class Users extends LoggedIn
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['edit_user'])) {
                $edit_user_id = (int)$_GET['edit_user'];
                $edit_name = $_POST['edit_name'];
                $edit_perm_level = $_POST['edit_perm_level'];
                $edit_active = isset($_POST['edit_active']) && $_POST['edit_active'] === '1';
                $edit_user = User::getById($edit_user_id);
                if ($edit_user !== null) {
                    $edit_user->update($edit_name, $edit_perm_level, $edit_active);
                } else {
                    ErrorHandler::instance()->addError('Kon gebruiker niet aanpassen: Niet gevonden.');
                }
            }
            if (isset($_POST['new_name']) && isset($_POST['new_perm_level'])) {
                $new_name = $_POST['new_name'];
                $new_perm_level = $_POST['new_perm_level'];
                $new_user = User::createNew($new_name, $new_perm_level);
                if ($new_user !== null) {
                    ErrorHandler::instance()->addMessage('Gebruiker aangemaakt.');
                }
            }
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.users', [
            'users' => User::getAll(),
            'role' => Session::instance()->getRole()
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [1, 2, 3];
    }
}
