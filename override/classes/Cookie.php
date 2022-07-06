<?php

class Cookie extends CookieCore {

	protected function _setcookie($cookie = null) {
		if(defined("IS_CLI")) {
			$_COOKIE[Context::getContext()->cookie->getName()] = $this->_cipherTool->encrypt($cookie);
		} else {
			parent::_setcookie($cookie);
		}
	}

}
