<?php
/**
 * COPS (Calibre OPDS PHP Server) class file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Sébastien Lucas <sebastien@slucas.fr>
 */

require_once dirname(__FILE__) . '/vendor/autoload.php';
require dirname(__FILE__) . '/config_default.php';
if (file_exists(dirname(__FILE__) . '/config_local.php') && (php_sapi_name() !== 'cli')) {
    require dirname(__FILE__) . '/config_local.php';
}
/** @var array $config */

use SebLucas\Cops\Input\Config;
use SebLucas\Cops\Input\Request;

$remote_user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
// Clean username, only allow a-z, A-Z, 0-9, -_ chars
$remote_user = preg_replace('/[^a-zA-Z0-9_-]/', '', $remote_user);
$user_config_file = 'config_local.' . $remote_user . '.php';
if (file_exists(dirname(__FILE__) . '/' . $user_config_file) && (php_sapi_name() !== 'cli')) {
    require dirname(__FILE__) . '/' . $user_config_file;
}

// from here on, we assume that all global $config variables have been loaded
Config::load($config);
date_default_timezone_set(Config::get('default_timezone'));

if (!Request::verifyLogin($_SERVER)) {
    header('WWW-Authenticate: Basic realm="COPS Authentication"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'This site is password protected';
    exit;
}

if (!function_exists('str_format')) {
    function str_format($format, ...$args)
    {
        return \SebLucas\Cops\Output\Format::str_format($format, ...$args);
    }
}

if (!function_exists('localize')) {
    $translator = new \SebLucas\Cops\Language\Translation($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null);
    Config::set('_translator_', $translator);

    function localize($phrase, $count=-1, $reset=false)
    {
        return Config::get('_translator_')->localize($phrase, $count, $reset);
    }
}
