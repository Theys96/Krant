<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\User;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Gebruikers pagina.
 */
class Users extends LoggedInPage
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_GET['activate'])) {
                $activate_user_id = (int)$_GET['activate'];
                $activate_user = User::getById($activate_user_id);
                if ($activate_user !== null) {
                    $activate_user->update($activate_user->username, $activate_user->perm_level, true, $activate_user->alt_css);
                } else {
                    ErrorHandler::instance()->addError('Kon gebruiker niet activeren: Niet gevonden.');
                }
            }
            if (isset($_GET['deactivate'])) {
                $deactivate_user_id = (int)$_GET['deactivate'];
                $deactivate_user = User::getById($deactivate_user_id);
                if ($deactivate_user !== null) {
                    $deactivate_user->update($deactivate_user->username, $deactivate_user->perm_level, false, $deactivate_user->alt_css);
                } else {
                    ErrorHandler::instance()->addError('Kon gebruiker niet deactiveren: Niet gevonden.');
                }
            }
            if (isset($_GET['edit_user'])) {
                $edit_user_id = (int)$_GET['edit_user'];
                $edit_name = $_POST['edit_name'];
                $edit_perm_level = $_POST['edit_perm_level'];
                $edit_active = isset($_POST['edit_active']) && $_POST['edit_active'] === '1';
                $edit_alt_css = $_POST['edit_alt_css'];
                $edit_user = User::getById($edit_user_id);
                if ($edit_user !== null) {
                    $edit_user->update($edit_name, $edit_perm_level, $edit_active, $edit_alt_css);
                } else {
                    ErrorHandler::instance()->addError('Kon gebruiker niet aanpassen: Niet gevonden.');
                }
            }
            if (isset($_GET['merge_user'])) {
                $user1_id = (int)$_GET['merge_user'];
                $user2_id = (int)$_POST['user2'];
                if ($user2_id != 0) {
                    $combine_user1 = User::getById($user1_id);
                    $combine_user2 = User::getById($user2_id);
                    if ($combine_user1 != null && $combine_user2 != null) {
                        $combine_user2->combineUsers($combine_user1);
                    } else {
                        ErrorHandler::instance()->addError('Kon de gebruikers niet mergen: Niet gevonden.');
                    }
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
        [$archived_users, $active_users] = [[], []];
        foreach (User::getAll() as $user) {
            if ($user->active) {
                $active_users[] = $user;
            } else {
                $archived_users[] = $user;
            }
        }

        return ViewRenderer::render_view('page.content.users', [
            'active_users' => $active_users,
            'archived_users' => $archived_users,
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
