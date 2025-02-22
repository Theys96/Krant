<?php

namespace App\Controller;

interface LoggedIn
{
    /**
     * @return int[]
     */
    public function allowed_roles(): array;
}
