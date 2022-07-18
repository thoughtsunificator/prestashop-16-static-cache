<?php

set_error_handler(function($num, $str, $file, $line, $context = null) {
	// error_log("$num, $str, $file, $line, $context");
});
set_exception_handler(function(Throwable $exception) {
  error_log("Uncaught exception: " , $exception->getMessage());
});
if(count($argv) >= 2) {
	if($argv[2] === "auth") {
		StaticCache::emulateAuth();
	}
}
StaticCache::cache($argv[1]);
