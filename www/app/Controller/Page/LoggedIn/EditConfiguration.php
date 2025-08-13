<?php

namespace App\Controller\Page\LoggedIn;

use App\Controller\Page\LoggedInPage;
use App\Util\Singleton\Configuration;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * Configuration pagina.
 */
class EditConfiguration extends LoggedInPage
{
    public function __construct()
    {
        if (3 === Session::instance()->getRole()) {
            if (isset($_POST['edit_schrijfregels'])) {
                $edit_schrijfregels = $_POST['edit_schrijfregels'];
                $edit_checks = (int) $_POST['edit_checks'];
                $edit_mail = $_POST['edit_mail'];
                $passwords = $_POST['edit_passwords'];
                $max_articles = $_POST['edit_max_articles'];
                $max_pictures = $_POST['edit_max_pictures'];
                $max_wjd = $_POST['edit_max_wjd'];
                $edit_mail = '' == $edit_mail ? null : $edit_mail;
                Configuration::instance()->updateAll($edit_schrijfregels, $edit_checks, $edit_mail, $passwords, $max_articles, $max_pictures, $max_wjd);
            }
        }
    }

    public function get_content(): string
    {
        return ViewRenderer::render_view('page.content.edit_configuration', [
            'variables' => Configuration::instance(),
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
