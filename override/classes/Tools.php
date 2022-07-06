<?php

class Tools extends ToolsCore {

	public static function displayError($string = 'Fatal error', $htmlentities = true, Context $context = null)
	{
		define("STATIC_CACHE_PAGE_ERROR", true);
		parent::displayError($string, $htmlentities , $context);
	}

	public static function display404Error()
	{
	   define("STATIC_CACHE_PAGE_ERROR", true);
	   parent::display404Error();
	}


}
