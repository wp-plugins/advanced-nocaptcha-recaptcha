<?php

if (!class_exists('anr_captcha_class'))
{
  class anr_captcha_class
  {
 	private static $instance;
	
	public static function init()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self;
            }
            return self::$instance;
        }
		
    function actions_filters()
    	{
			if ( '1' == anr_get_option( 'fep_contact_form' )) {
					add_action ('fepcf_message_form_after_content', array(&$this, 'form_field'), 99);
					add_action ('fepcf_action_message_before_send', array(&$this, 'fepcf_verify'));
				}
			
			if ( '1' == anr_get_option( 'login' ) && !defined('XMLRPC_REQUEST')) {
					add_action ('login_form', array(&$this, 'form_field'), 99);
					add_filter ('authenticate', array(&$this, 'login_verify'), 999 );
				}
			
			if ( '1' == anr_get_option( 'registration' )) {
					add_action ('register_form', array(&$this, 'form_field'), 99);
					add_filter ('registration_errors', array(&$this, 'registration_verify'), 10, 3 );
				}
			
			if ( '1' == anr_get_option( 'lost_password' )) {
					add_action ('lostpassword_form', array(&$this, 'form_field'), 99);
					add_action ('allow_password_reset', array(&$this, 'lostpassword_verify'), 10, 2); //lostpassword_post does not return wp_error
				}
				
			if ( '1' == anr_get_option( 'reset_password' )) {
					add_action ('resetpass_form', array(&$this, 'form_field'), 99);
					add_filter ('validate_password_reset', array(&$this, 'reset_password_verify'), 10, 2 );
				}
					
			if ( '1' == anr_get_option( 'comment' )) {
					add_filter ('comment_form_field_comment', array(&$this, 'comment_form_field') );
					add_filter ('preprocess_comment', array(&$this, 'comment_verify') );
				}
			
			if ( function_exists( 'wpcf7_add_shortcode' )) {
					wpcf7_add_shortcode('anr_nocaptcha', array(&$this, 'wpcf7_form_field'), true);
					add_filter('wpcf7_validate_anr_nocaptcha', array(&$this, 'wpcf7_verify'), 10, 2);
				}
				
			if ( '1' == anr_get_option( 'bb_new' )) {
					add_action ('bbp_theme_before_topic_form_submit_wrapper', array(&$this, 'form_field'), 99);
					add_action ('bbp_new_topic_pre_extras', array(&$this, 'bb_new_verify') );
				}
				
			if ( '1' == anr_get_option( 'bb_reply' )) {
					add_action ('bbp_theme_before_reply_form_submit_wrapper', array(&$this, 'form_field'), 99);
					add_action ('bbp_new_reply_pre_extras', array(&$this, 'bb_reply_verify'), 10, 2 );
				}
    	}
		
	
	function form_field()
		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return;
				
			anr_captcha_form_field();
			
		}
		
	function comment_form_field( $defaults )
		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return $defaults;
				
				$defaults = $defaults. '<br />' .anr_captcha_form_field( false );
				return $defaults;
			
			
		}
		
	function verify()
		{
			
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return true;
				
			return anr_verify_captcha();
			
		}
		
	function fepcf_verify ( $errors )
		{
			$error_message = str_replace(__('<strong>ERROR</strong>: ', 'anr'), '', anr_get_option( 'error_message' ));
			
			if ( ! $this->verify() )
			
			$errors->add('anr_error', $error_message);
		}
		
	function login_verify ( $user )
		{
			if ( ! $this->verify() ) {
			$error_message = anr_get_option( 'error_message' );
			return new WP_Error( 'anr_error', $error_message );
			}
			
			return $user;
		}
		
	function registration_verify (  $errors, $sanitized_user_login, $user_email )
		{
			if ( ! $this->verify() ) {
			$error_message = anr_get_option( 'error_message' );
			$errors->add( 'anr_error', $error_message );
			}
			
			return $errors;
		}
		
	function lostpassword_verify( $result, $user_id )
		{
			if ( ! $this->verify() ) {
			$error_message = anr_get_option( 'error_message' );
			return new WP_Error( 'anr_error', $error_message );
			}
			
			return $result;
		}
		
		
	function reset_password_verify( $errors, $user )
		{
			
			if ( ! $this->verify() ) {
			$error_message = anr_get_option( 'error_message' );
			$errors->add('anr_error', $error_message);
				}
		}
		
	function comment_verify( $commentdata )
		{
			
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				wp_die( $error_message, 200 );
			}
			
			return $commentdata;
		}
		
	function wpcf7_form_field( $tags )
		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return;
				
				return anr_captcha_form_field( false )."<span class='wpcf7-form-control-wrap g-recaptcha-response'></span>";
			
			
		}
		
	function wpcf7_verify( $result, $tag  )
		{
			$tag = new WPCF7_Shortcode( $tag );
			$name = $tag->name;
			
			if ( ! $this->verify() ) {
			
				$error_message = anr_get_option( 'error_message' ).'<button onclick="javascript:location.reload();">Reload Captcha</button>';
				
				if ( method_exists($result, 'invalidate' ) ) { // wpcf7 4.1
				$result->invalidate( $tag, $error_message );
			} else {
				$result['valid'] = false;
				$result['reason'][$name] = $error_message;
			}
				}

		return $result;
		}
		
	function bb_new_verify( $forum_id )
		{
			
			if ( ! $this->verify() ) {
			$error_message = anr_get_option( 'error_message' );
			bbp_add_error('anr_error', $error_message);
				}
		}
		
	function bb_reply_verify( $topic_id, $forum_id )
		{
			
			if ( ! $this->verify() ) {
			$error_message = anr_get_option( 'error_message' );
			bbp_add_error('anr_error', $error_message);
				}
		}


	
  } //END CLASS
} //ENDIF

add_action('init', array(anr_captcha_class::init(), 'actions_filters'));
?>