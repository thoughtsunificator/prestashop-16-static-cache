<?php
require_once(dirname(__FILE__). "/../config/static-cache.php");
set_error_handler(function($num, $str, $file, $line, $context = null) {
	// error_log("$num, $str, $file, $line, $context");
});
set_exception_handler(function(Throwable $exception) {
  error_log("Uncaught exception: " , $exception->getMessage());
});
$auth = false;
if(count($argv) >= 2) {
	$auth = $argv[2] === "auth";
}
StaticCache::cache(["url" => $argv[1], "auth" => false]);
