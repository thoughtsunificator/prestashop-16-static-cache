<?php

set_error_handler(function($num, $str, $file, $line, $context = null) {
	// error_log("$num, $str, $file, $line, $context");
});
set_exception_handler(function(Throwable $exception) {
  error_log("Uncaught exception: " , $exception->getMessage());
});
ini_set( "display_errors", "off" );
define("IS_CLI", true);
define('_PS_MODE_DEV_', false);
define('_PS_DISPLAY_COMPATIBILITY_WARNING_', false);
define('_PS_DEBUG_SQL_', false);
define('_PS_DEBUG_PROFILING_', false);
require_once(dirname(__FILE__). "/../config/static-cache.php");
require_once(dirname(__FILE__).'/../config/config.inc.php');
StaticCache::emulateBrowser();
$strURL = $argv[1];
$url = parse_url($strURL);
$_SERVER['REQUEST_URI'] = $strURL;
$_SERVER['QUERY_STRING'] = $url["query"];
parse_str($url["query"], $_GET);
if(count($argv) >= 2) {
	if($argv[2] === "auth") {
		StaticCache::emulateAuth();
	}
}
Dispatcher::getInstance()->dispatch();
