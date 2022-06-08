<?php
namespace Controller\Page;

use Util\Singleton\Session;

class Logout extends Login
{
    public function __construct()
    {
        Session::instance()->reset(); 
    }
}
?>
