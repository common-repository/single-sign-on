<?php
require(dirname(__FILE__).'/connectmaster.class.php');
/**
 * Wordpress SSO connect class
 * @package SSO_Wordpress
 *
 */
class sso_connect extends sso_connect_master {
	function log($msg) {
		if (is_array($msg)) $msg=print_r($msg,true);
		//if (get_option('sso_bridge_debug')) {
			$v=get_option('sso_log');
			if (!is_array($v)) $v=array();
			array_unshift($v,array(time(),$msg));
			if (count($v) > 100) array_pop($v);
			update_option('sso_log',$v);
		//}
	}

	function url() {
		$url=get_option('sso_url') ? get_option('sso_url') : 'http://sso.clientcentral.info/';
		if (substr($url,-1) != '/') $url.='/';
		return $url;
	}

	function is_user_logged_in() {
		return is_user_logged_in();
	}

	function get_key() {
		return get_option('sso_key');
	}

	function hash() {
		return md5(get_option('sso_key'));
	}

	function login_url($redirect='') {
		return str_replace('&amp;','&',wp_login_url($redirect));
	}

	function logout_url($redirect='') {
		return str_replace('&amp;','&',wp_logout_url($redirect));
	}

	function invalidate() {
		return 0;
		return isset($_SESSION['cdssoinvalidate']) ? $_SESSION['cdssoinvalidate'] : 0;
	}

	function setInvalidate() {
		$_SESSION['cdssoinvalidate']=1;
	}

	function unsetInvalidate() {
		$_SESSION['cdssoinvalidate']=0;
	}
	
}
