<?php 
namespace Util;

/**
 * Configuration holder.
 *
 * Copy this file to Config.class.php and fill in the correct values.
 */
class Config
{
    const BASE_URL = 'http://localhost/';
    const MYSQL_HOST = 'db';
    const MYSQL_USER = 'thijs';
    const MYSQL_PASSWORD = 'krant';
    const MYSQL_DB = 'krant';
    const PASSWORDS = [
        1 => null,
        2 => null,
        3 => "printer"
    ];
}
