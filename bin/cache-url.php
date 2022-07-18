<?php

require_once(dirname(__FILE__). "/../config/static-cache.php");
$auth = false;
if(count($argv) >= 2) {
	$auth = $argv[2] === "auth";
}
StaticCache::cache(["url" => $argv[1], "auth" => false]);
