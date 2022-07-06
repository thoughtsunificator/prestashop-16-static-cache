<?php

class FrontController extends FrontControllerCore {

	protected function canonicalRedirection($canonical_url = '') {
		if(!defined("IS_CLI")) {
			parent::canonicalRedirection($canonical_url);
		}
	}

	public function display() {
		if(!defined('IS_CLI')) {
			parent::display();
		} else {
			ob_start();
			parent::display();
			$str = ob_get_contents();
			ob_clean();
			$key = StaticCache::getKey();
			if(!defined("STATIC_CACHE_PAGE_ERROR") && $key !== null) {
				StaticCache::$MEMCACHED->set($key, $str);
			}
			echo $str;
		}
	}

}

