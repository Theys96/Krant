<?php

namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedInPage;
use Model\SingleVariables;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * Categorieën pagina.
 */
class EditVariables extends LoggedInPage
{
    public function __construct()
    {
        if (Session::instance()->getRole() === 3) {
            if (isset($_POST['edit_schrijfregels'])) {
                $edit_schrijfregels = $_POST['edit_schrijfregels'];
                $edit_checks = (int)$_POST['edit_checks'];
                $edit_mail = $_POST['edit_mail'];
                if($edit_mail === "") {
                    $edit_mail = null;
                }
                $edit_variables = SingleVariables::instance();
                if ($edit_variables !== null) {
                    $edit_variables->update($edit_schrijfregels, $edit_checks, $edit_mail);
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
        return ViewRenderer::render_view('page.content.edit_variables', [
            'variables' => SingleVariables::instance(),
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
