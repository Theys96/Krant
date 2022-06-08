<?php
namespace Controller\Page\LoggedIn;

use Controller\Page\LoggedIn;

class Home extends LoggedIn
{
    public function get_content(): string
    {
        return '<pre>' . var_export($_SESSION, true) . '</pre>';
    }
}
?>