<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Model\User;
use App\Util\Singleton\ErrorHandler;
use App\Util\ViewRenderer;

/**
 * Gebruiker aanpassen.
 */
class EditUser extends LoggedInPage
{
    protected User $user;

    public function __construct()
    {
        if (isset($_GET['user'])) {
            $user = User::getById((int) $_GET['user']);
            if (null !== $user) {
                $this->user = $user;

                return;
            }
        }
        ErrorHandler::instance()->addError('Gebruiker niet gevonden.');
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.edit_user', [
            'user' => $this->user,
            'users' => User::getAll(),
        ]);
    }

    /**
     * @return int[]
     */
    public function allowed_roles(): array
    {
        return [3];
    }
}
