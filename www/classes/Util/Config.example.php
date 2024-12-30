<?php

namespace Util;

/**
 * Configuration holder.
 *
 * Copy this file to Config.class.php and fill in the correct values.
 */
class Config
{
    public const BASE_URL = 'http://localhost/';
    public const MYSQL_HOST = 'db';
    public const MYSQL_USER = 'thijs';
    public const MYSQL_PASSWORD = 'krant';
    public const MYSQL_DB = 'krant';
    public const PASSWORDS = [
        1 => null,
        2 => null,
        3 => "printer"
    ];
}
