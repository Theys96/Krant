<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Util\Singleton\Configuration;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class EditConfiguration extends LoggedInPage
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_POST['edit_schrijfregels'])) {
                $edit_schrijfregels = $_POST['edit_schrijfregels'];
                $edit_checks = (int)$_POST['edit_checks'];
                $edit_mail = $_POST['edit_mail'];
                $passwords = $_POST['edit_passwords'];
                $edit_mail = $edit_mail == "" ? null : $edit_mail;
                $edit_variables = Configuration::instance();
                if ($edit_variables !== null) {
                    $edit_variables->updateAll($edit_schrijfregels, $edit_checks, $edit_mail, $passwords);
                } else {
                    ErrorHandler::instance()->addError('Kon losse variabelen niet aanpassen: Niet gevonden.');
                }
            }
        }
    }

    /**
     * @return string
     */
    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.edit_configuration', [
            'variables' => Configuration::instance()->getComplete(),
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
