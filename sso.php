<?php
/*
 Plugin Name: Single Sign On
 Plugin URI: https://github.com/choppedcode
 Description: Single Sign On allows a user to only sign in once and be signed in automatically across different systems
 Author: choppedcode
 Version: 1.0.4
 Author URI: https://github.com/choppedcode
 */

require(dirname(__FILE__).'/controlpanel.php');
require(dirname(__FILE__).'/includes/connect.class.php');
if (get_option('sso_key')) {
	add_action("init","sso_init");
	add_action('wp_login','sso_login');
	add_action('wp_logout','sso_logout');
	add_filter('authenticate', 'sso_authenticate', 10, 3 );
	add_action('wp_footer', 'sso_footer');
}

/*
 * Start session and enqueue relevant scripts
 */
function sso_init() {
	if (!session_id()) session_start();
	if (!is_admin()) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquerycookie',plugins_url('single-sign-on').'/js/cookie/jquery.cookie.js');
		wp_enqueue_script('easyXDM2','http://23dbd2b813a5bbdcf94f-ad7ae67fe93983be12b9863528b62e99.r9.cf1.rackcdn.com/easyXDM.min.js',array('jquery'));
	}
}

/*
 * Generate iframe and javascript to manage cookie
 */
function sso_footer() {
	$redirect=get_permalink();
	$loginUrl=sso_connect::login_url($redirect);
	$content=sso_connect::frame($redirect,$loginUrl);
	echo $content;
}

/*
 * Check if the user is already logged in via SSO
 */
function sso_authenticate($user, $username, $password) {
	$sso=new sso_connect();
	if (!$user) {
		sso_connect::log('Authenticate, first pass');
		$cookie=isset($_COOKIE['cdsso']) ? $_COOKIE['cdsso'] : '';
		$result=$sso->connect('loggedin',array('cookie'=>$cookie));
		if (isset($result['loggedin']) && $result['loggedin'] && isset($result['email'])) {
			$user=get_user_by('email',$result['email']);
			if ($user) return $user;
		}
		sso_connect::setInvalidate();
	}
	return;
}

/*
 * User is logged in
 */
function sso_login($login) {
	sso_connect::log('Login');
	$current_user=get_user_by('login', $login);
	$sso=new sso_connect();
	$result=$sso->connect('login',array('username'=>$current_user->user_login, 'password'=>$_REQUEST['pwd'], 'email' => $current_user->user_email));
	sso_connect::unsetInvalidate();
}

/*
 * User logs out
 */
function sso_logout() {
	global $current_user;
	$sso=new sso_connect();
	$sso->connect('logout');
}
