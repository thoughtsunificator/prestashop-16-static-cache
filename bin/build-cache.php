<?php

set_time_limit(0);
error_reporting( E_ALL );
define("IS_CLI", true);
require_once(dirname(__FILE__). "/../config/static-cache.php");
require_once(dirname(__FILE__).'/../config/config.inc.php');
StaticCache::emulateBrowser();
$resume = false;
$auth = false;
if(count($argv) >= 2) {
	if($argv[1] === "resume") {
		$resume = true;
		echo "Resuming cache building...\n";
	} else if($argv[1] === "auth") {
		$auth = true;
		echo "Auth enabled.\n";
	}
}
$urls = StaticCache::$DEFAULT_URLS;
$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` where active = 1 and is_root_category = 0 and id_parent != 0');
foreach($categories as $key => $value) {
	array_push($urls, ["url" => "/index.php?controller=category&id_category=". $value["id_category"], "auth" => false]);
	array_push($urls, ["url" => "/index.php?controller=category&id_category=". $value["id_category"], "auth" => true]);
}
$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'product` where active = 1');
foreach($products as $key => $value) {
	array_push($urls, ["url" => "/index.php?controller=product&id_product=". $value["id_product"], "auth" => false]);
	array_push($urls, ["url" => "/index.php?controller=product&id_product=". $value["id_product"], "auth" => true]);
}
if($resume) {
	$countBefore = count($urls);
	$urls = array_filter($urls, function($element) {
		return !StaticCache::$MEMCACHED->get($element["url"]);
	});
	echo "Skipping ".($countBefore - count($urls)). " urls\n";
}
$time_start = microtime(true);
foreach($urls as $key => $url) {
	StaticCache::cache($url);
	echo "Progress ".($key + 1)." / ".count($urls). "\n";
}
$time_end = microtime(true);
$time = $time_end - $time_start;
echo "Cache built in $time seconds !\n";
