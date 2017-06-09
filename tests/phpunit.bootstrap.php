<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Vancouver');

$site_path = realpath(dirname(__FILE__)); // strip the /web from it
$site_path = str_replace('/tests', '', $site_path);

define('__SITE_PATH', $site_path);
define('__CACHE_DIRECTORY', $site_path . '/app/cache');
define('__DEBUG_OUTPUT_PATH', '/var/www/ip2/binghan.com/logs/phpunit.log');
define('__CONFIG_PATH', $site_path . '/app/config/');
//include_once('phpunit.configuration.php');
require_once(__SITE_PATH . '/vendor/composer/ClassLoader.php');
require_once 'phpunit.systemfunctions.php';
$loader = new Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('components', __SITE_PATH . '/src');
$loader->add('extensions', __SITE_PATH . '/src');
$loader->add('core', __SITE_PATH . '/app');
$loader->add('exceptions', __SITE_PATH . '/app/');
$loader->add('Gossamer\\Aset', __SITE_PATH . '/vendor/gossamer/aset/src');
$loader->add('Gossamer\\Caching', __SITE_PATH . '/vendor/gossamer/caching/src');
$loader->add('Gossamer\\Horus', __SITE_PATH . '/vendor/gossamer/horus/src');
$loader->add('Gossamer\\Neith', __SITE_PATH . '/vendor/gossamer/neith/src');
$loader->add('Gossamer\\Nephthys', __SITE_PATH . '/vendor/gossamer/nephthys/src');
$loader->add('Gossamer\\Pesedget', __SITE_PATH . '/vendor/gossamer/pesedget/src');
$loader->add('Gossamer\\Pesedget-Couchbase', __SITE_PATH . '/vendor/gossamer/pesedget-couchbase/src');
$loader->add('Gossamer\\Ra', __SITE_PATH . '/vendor/gossamer/ra/src');
$loader->add('Gossamer\\Set', __SITE_PATH . '/vendor/gossamer/set/src');
$loader->add('RestClient', __SITE_PATH . '/vendor/tcdent/php-restclient');
$loader->add('tests', __SITE_PATH . '/tests');

$loader->add('Monolog', __SITE_PATH . '/vendor/monolog/monolog/src');

// activate the autoloader
$loader->register();

// to enable searching the include path (eg. for PEAR packages)
$loader->setUseIncludePath(true);

function super_unset($item) {
    try {
        if (is_object($item) && method_exists($item, "__destruct")) {
            $item->__destruct();
        }
    } catch (\Exception $e) {

    }
    //unset($item);
    $item = null;
}

$_SESSION = array();

function getSession($key = null) {
    echo "getting sesson";
    $session = $_SESSION;
    echo "got it";
    if(is_null($key)) {
        echo "return it";
        echo gettype($session);
        return $session;
    }
    return fixObject($session[$key]);
}

function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

function fixObject(&$object) {
    if (!is_object($object) && gettype($object) == 'object') {

        return ($object = unserialize(serialize($object)));
    }

    return $object;
}























//echo "\r\n********** phpunit.bootstrap complete *************\r\n";
