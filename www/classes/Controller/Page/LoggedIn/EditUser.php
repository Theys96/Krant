<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;
use Model\User;
use Util\Singleton\ErrorHandler;
use Util\ViewRenderer;

/**
 * Gebruiker aanpassen.
 */
class EditUser extends LoggedIn
{
    /** @var User */
    protected User $user;

    public function __construct()
    {
        if (isset($_GET['user'])) {
            $user = User::getById((int)$_GET['user']);
            if ($user !== null) {
                $this->user = $user;
                return;
            }
        }
        ErrorHandler::instance()->addError('Gebruiker niet gevonden.');
    }


    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.edit_user', [
            'user' => $this->user,
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
