<?php
/*
Plugin Name: Basic Authentication Setting
Plugin URI: http://branu.jp/
Description: A BASIC Authentication setting can be performed from an admin screen.
Version: 1.0
Author: BRANU
Author URI: http://branu.jp/
*/


function add_general_custom_fields_auth_basic() {
	add_settings_field( 'auth_basic', 'BASIC認証のパスワード<br /><small>[id:password]の形式で入力してください。</small>', 'custom_auth_basic_field', 'general', 'default', array( 'label_for' => 'auth_basic' ) );
}
add_action( 'admin_init', 'add_general_custom_fields_auth_basic' );

function custom_auth_basic_field( $args ) {
	$auth_basic = get_option( 'auth_basic' );
	echo '<input type="text" name="auth_basic" id="auth_basic" size="30" value="' . esc_html( $auth_basic ) . '" />';
}



function display_custom_fields_auth_basic( $whitelist_options ) {
	$whitelist_options['general'][] = 'auth_basic';
	
	return $whitelist_options;
}
add_filter( 'whitelist_options', 'display_custom_fields_auth_basic' );





//BASIC認証
function basic_auth($auth_list,$realm="Restricted Area",$failed_text="ログインできませんでした"){
	if (isset($_SERVER['PHP_AUTH_USER']) and isset($auth_list[$_SERVER['PHP_AUTH_USER']])){
		if ($auth_list[$_SERVER['PHP_AUTH_USER']] == $_SERVER['PHP_AUTH_PW']){
			$value = 1;
			$timeout = 0;
			setcookie('BASIC',$value,$timeout,'/');
			
			return $_SERVER['PHP_AUTH_USER'];
		}
	}
	
	header('WWW-Authenticate: Basic realm="'.$realm.'"');
	header('HTTP/1.0 401 Unauthorized');
	header('Content-type: text/html; charset='.mb_internal_encoding());
	
	exit($failed_text);
}

function is_login_page() {
	if (basename($_SERVER['PHP_SELF']) == 'wp-login.php') {
		return true;
	} else {
		return false;
	}
}

//BASIC認証実行
function add_auth_basic() {
	//管理画面、ログイン画面は除外
	if ( !is_admin() && !is_login_page() ) {
		if(get_option('auth_basic') != ''){
			$basic = explode(':', get_option('auth_basic'));
			basic_auth(array($basic[0] => $basic[1]));
		}
	}
}
add_action('init', add_auth_basic);