<?php
/*
Plugin Name: Advanced noCaptcha reCaptcha
Plugin URI: https://shamimbiplob.wordpress.com/contact-us/
Description: Show noCaptcha in Login, Register, Lost Password, Reset Password, Comment Form (after Comment textarea before submit button). Also can implement in any other form easily.
Version: 1.2
Author: Shamim
Author URI: https://shamimbiplob.wordpress.com/contact-us/
Text Domain: anr
License: GPLv2 or later
*/
//DEFINE
define('ANR_PLUGIN_DIR',plugin_dir_path( __FILE__ ));
define('ANR_PLUGIN_URL',plugins_url().'/advanced-nocaptcha-recaptcha/');

require_once('functions.php');


	//ADD ACTIONS
	
	add_action('after_setup_theme', 'anr_include_require_files');
	add_action('plugins_loaded', 'anr_translation');
	add_action('wp_enqueue_scripts', 'anr_enqueue_scripts');
	add_action('login_enqueue_scripts', 'anr_login_enqueue_scripts');
	

