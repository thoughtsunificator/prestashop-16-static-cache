<?php

require_once(_PS_ROOT_DIR_. "/config/static-cache.php");

class Dispatcher extends DispatcherCore {

	public function dispatch() {
		if(!defined('IS_CLI') && $_SERVER["REQUEST_METHOD"] === "GET") {
			$key = StaticCache::getKey();
			$str = StaticCache::get($key);
			if($str !== false) {
				echo $str;
			} else {
				parent::dispatch();
			}
		} else {
			parent::dispatch();
		}

	}

}
