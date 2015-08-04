<?php


if ( !function_exists('anr_get_option') ) :
	
function anr_get_option( $option, $default = '', $section = 'anr_admin_options' ) {
	
    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}
	
endif;
	
function anr_translation()
	{
	//SETUP TEXT DOMAIN FOR TRANSLATIONS
	load_plugin_textdomain('anr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
function anr_enqueue_scripts()
    {
		$language	= trim(anr_get_option( 'language' ));
		
		$lang	= "";
		if ( $language )
			$lang = "?hl=$language";
			
		wp_register_script( 'anr-google-recaptcha-script', "https://www.google.com/recaptcha/api.js$lang", array(), '2.0', true );
		
	}
	
function anr_login_enqueue_scripts()
    {
		$language	= trim(anr_get_option( 'language' ));
		$remove_css	= trim(anr_get_option( 'remove_css' ));
		
		$lang	= "";
		if ( $language )
			$lang = "?hl=$language";
			
		wp_register_script( 'anr-google-recaptcha-script', "https://www.google.com/recaptcha/api.js$lang", array(), '2.0', true );
		
		if ( !$remove_css )
		wp_enqueue_style( 'anr-login-style', ANR_PLUGIN_URL . 'style/style.css' );
		
	}
	
function anr_include_require_files() 
	{
	if ( is_admin() ) 
		{
			$fep_files = array(
							'admin' => 'admin/anr-admin-class.php'
							);
										
		} else {
			$fep_files = array(
							'main' => 'anr-captcha-class.php'
							);
				}
					
	$fep_files = apply_filters('anr_include_files', $fep_files );
	
	foreach ( $fep_files as $fep_file ) {
	require_once ( $fep_file );
		}
	}

function anr_captcha_form_field( $echo = true )
	{
		$site_key 	= trim(anr_get_option( 'site_key' ));
		$theme		= anr_get_option( 'theme', 'light' );
		$size		= anr_get_option( 'size', 'normal' );
		$no_js		= anr_get_option( 'no_js' );
		
		if ( !wp_script_is( 'anr-google-recaptcha-script', 'registered' ) )
			{
				$language	= trim(anr_get_option( 'language' ));
		
				$lang	= "";
				if ( $language )
					$lang = "?hl=$language";
					
				wp_register_script( 'anr-google-recaptcha-script', "https://www.google.com/recaptcha/api.js$lang", array(), '2.0', true );
				
			}
			
		wp_enqueue_script('anr-google-recaptcha-script');
		
		$field 		= "<div class='g-recaptcha' data-sitekey='$site_key' data-theme='$theme' data-size='$size'></div>";
		
		if ( $no_js == 1 )
			{
				$field .="<noscript>
  							<div style='width: 302px; height: 352px;'>
    							<div style='width: 302px; height: 352px; position: relative;'>
      							<div style='width: 302px; height: 352px; position: absolute;'>
        							<iframe src='https://www.google.com/recaptcha/api/fallback?k=$site_key'
                							frameborder='0' scrolling='no'
                							style='width: 302px; height:352px; border-style: none;'>
        							</iframe>
      							</div>
								  <div style='width: 250px; height: 80px; position: absolute; border-style: none;
											  bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;'>
									<textarea id='g-recaptcha-response' name='g-recaptcha-response'
											  class='g-recaptcha-response'
											  style='width: 250px; height: 80px; border: 1px solid #c1c1c1;
													 margin: 0px; padding: 0px; resize: none;' value=''>
									</textarea>
								  </div>
								</div>
							  </div>
							</noscript>";
				}
		
		if ( $echo )
			echo $field;
			
		return $field;
		
	}
	
function anr_verify_captcha()
	{
		$secre_key 	= trim(anr_get_option( 'secret_key' )); 
		$response = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
		$remoteip = $_SERVER["REMOTE_ADDR"];
		
		if ( !$secre_key ) //if $secre_key is not set
			return true;
		
		if ( !$response || !$remoteip )
			return false;
		
		$url = "https://www.google.com/recaptcha/api/siteverify";

		// make a POST request to the Google reCAPTCHA Server
		$request = wp_remote_post( $url, array('body' => array( 'secret' => $secre_key, 'response' => $response, 'remoteip' => $remoteip ) ) );

		if ( is_wp_error( $request ) )
   			return false;

		// get the request response body
		$request_body = wp_remote_retrieve_body( $request );
			if ( !$request_body )
				return false;

		$result = json_decode( $request_body, true );
		 if ( isset($result['success']) && true == $result['success'] )
		 	return true;

		return false;
	}
	
add_filter('shake_error_codes', 'anr_add_shake_error_codes' );

function anr_add_shake_error_codes( $shake_error_codes )
	{
		$shake_error_codes[] = 'anr_error';
		
		return $shake_error_codes;
	}
	
