<?php

namespace App\Controller;

/**
 * Interface voor renderbare pagina controllers.
 */
interface Response
{
    /**
     * @return string
     */
    public function render(): string;
}
