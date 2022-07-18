<?php

final class StaticCache {

	public static $SERVER_NAME = "localhost";
	public static $PARALLEL = 100;
	public static $KEY_ID = "my_site_";
	public static $HTTP_HOST = "localhost:8041";
	public static $HTTP_USER_AGENT = "Mozilla/5.0 (X11; Linux x86_64; rv:101.0) Gecko/20100101 Firefox/101.0";
	public static $MAP = [
		["controller-slug" => "category", "controller" => "category", "targetQueryParameter" => "id_category", "path" => "/index.php" ],
		["controller-slug" => "my-account", "controller" => "myaccount", "path" => "/index.php" ],
		["controller-slug" => "product", "controller" => "product", "targetQueryParameter" => "id_product", "path" => "/index.php" ],
		["controller-slug" => "index", "controller" => "index", "alias" => "", "blacklistQueryParameters" => ["mylogout"], "path" => "/index.php" ],
		["controller-slug" => "prices-drop", "controller" => "pricesdrop", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "new-products", "controller" => "newproducts", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "best-sales", "controller" => "bestsales", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "stores", "controller" => "stores", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "contact", "controller" => "contact", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "cms", "controller" => "cms", "targetQueryParameter" => "id_cms", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "authentication", "controller" => "authentication", "blacklistQueryParameters" => [], "path" => "/index.php" ],
		["controller-slug" => "sitemap", "controller" => "sitemap", "blacklistQueryParameters" => [], "path" => "/index.php" ]
	];
	public static $DEFAULT_URLS = [
		[ "url" => "/index.php?controller=index", "auth" => false ],
		[ "url" => "/index.php?controller=prices-drop", "auth" => false ],
		[ "url" => "/index.php?controller=new-products", "auth" => false ],
		[ "url" => "/index.php?controller=best-sales", "auth" => false ],
		[ "url" => "/index.php?controller=stores", "auth" => false ],
		[ "url" => "/index.php?controller=contact", "auth" => false ],
		[ "url" => "/index.php?controller=authentication", "auth" => false ],
		[ "url" => "/index.php?controller=cms&id_cms=1", "auth" => false ],
		[ "url" => "/index.php?controller=cms&id_cms=2", "auth" => false ],
		[ "url" => "/index.php?controller=cms&id_cms=3", "auth" => false ],
		[ "url" => "/index.php?controller=cms&id_cms=4", "auth" => false ],
		[ "url" => "/index.php?controller=sitemap", "auth" => false ],
		[ "url" => "/index.php?controller=index", "auth" => true ],
		[ "url" => "/index.php?controller=prices-drop", "auth" => true ],
		[ "url" => "/index.php?controller=new-products", "auth" => true ],
		[ "url" => "/index.php?controller=best-sales", "auth" => true ],
		[ "url" => "/index.php?controller=stores", "auth" => true ],
		[ "url" => "/index.php?controller=contact", "auth" => true ],
		[ "url" => "/index.php?controller=authentication", "auth" => true ],
		[ "url" => "/index.php?controller=cms&id_cms=1", "auth" => true ],
		[ "url" => "/index.php?controller=cms&id_cms=2", "auth" => true ],
		[ "url" => "/index.php?controller=cms&id_cms=3", "auth" => true ],
		[ "url" => "/index.php?controller=cms&id_cms=4", "auth" => true ],
		[ "url" => "/index.php?controller=sitemap", "auth" => true ],
		[ "url" => "/index.php?controller=my-account", "auth" => true ]
	];
	public static $MEMCACHED = null;

	private function __construct() {}

	public static function emulateBrowser() {
		$_SERVER = [];
		$_GET = [];
		$_COOKIE = [];
		$_REQUEST = [];
		$_SERVER["REQUEST_METHOD"] = "GET";
		$_SERVER["SERVER_NAME"] = self::$SERVER_NAME;
		$_SERVER["SCRIPT_NAME"] = "/index.php";
		$_SERVER["HTTP_HOST"] = self::$HTTP_HOST;
		$_SERVER["HTTP_USER_AGENT"] = self::$HTTP_USER_AGENT;
	}

	/**
	 * @param string $url
	 */
	public static function cache($url) {
		require_once(dirname(__FILE__). "/../config/static-cache.php");
		require_once(dirname(__FILE__).'/../config/config.inc.php');
		ini_set("error_reporting", "E_ERROR | E_WARNING | E_PARSE");
		echo "Attempting to cache ".$url["url"]. ($url["auth"] ? " (auth)" : "")."\n";
		define("IS_CLI", true);
		define('_PS_MODE_DEV_', false);
		define('_PS_DISPLAY_COMPATIBILITY_WARNING_', false);
		define('_PS_DEBUG_SQL_', false);
		define('_PS_DEBUG_PROFILING_', false);
		define('_PS_SMARTY_CACHE_', null);
		define('_PS_SMARTY_FORCE_COMPILE_', 1);
		StaticCache::emulateBrowser();
		$parsedURL = parse_url($url["url"]);
		$_SERVER['REQUEST_URI'] = $url["url"];
		$_SERVER['QUERY_STRING'] = $parsedURL["query"];
		parse_str($parsedURL["query"], $_GET);
		Dispatcher::getInstance()->dispatch();
	}

	public static function emulateAuth() {
		$customer = new Customer(1);
		$customer->logged = 1;
		Context::getContext()->cookie->customer_firstname = "Your";
		Context::getContext()->cookie->customer_lastname = "account";
		Context::getContext()->customer = $customer;
		Context::getContext()->cookie->write();
	}

	public static function get($key) {
		return StaticCache::$MEMCACHED->get($key);
	}

	public static function set($key, $str) {
		StaticCache::$MEMCACHED->set($key, $str);
	}

	/**
	 * @return string
	 */
	public static function getKey() {
		$controller = Dispatcher::getInstance()->getController();
		if(defined("_PS_ADMIN_DIR_")) {
			return null;
		}
		$mapEntry = current(array_filter(self::$MAP, function($element) use($controller) {
			return $element["controller"] === $controller || ($element["alias"] === $controller);
		}));
		$url = parse_url($_SERVER["REQUEST_URI"]);
		if( $mapEntry !== false
			&& (!array_key_exists("targetQueryParameter", $mapEntry) || array_key_exists($mapEntry["targetQueryParameter"], $_GET))
			&& $_SERVER["REQUEST_METHOD"] === "GET") {
			foreach($mapEntry["blacklistQueryParameters"] as $blackListedParam) {
				if(array_key_exists($blackListedParam, $_GET)) {
					return null;
				}
			}
			$key = self::$KEY_ID . $mapEntry["path"];
			$params = [];
			if($mapEntry["controller"] !== "") {
				$params["controller"] = $mapEntry["controller-slug"];
			}
			if(array_key_exists("targetQueryParameter", $mapEntry)) {
				$params[$mapEntry["targetQueryParameter"]] = $_GET[$mapEntry["targetQueryParameter"]];
			}
			if(count($params) >= 1) {
				$key .= "?". http_build_query($params);
			}
			if(Context::getContext()->customer->logged) {
				$key .= "_auth";
			}
			return $key;
		}
	}

}

StaticCache::$MEMCACHED = new Memcached();
StaticCache::$MEMCACHED->addServer('127.0.0.1',11211);
