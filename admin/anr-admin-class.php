<?php

if (!class_exists('anr_admin_class'))
{
  class anr_admin_class
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
		if ( is_multisite() ) {
			$same_settings = apply_filters( 'anr_same_settings_for_all_sites', false );
		} else {
			$same_settings = false;
		}
		if ( $same_settings ) {
			add_action('network_admin_menu', array(&$this, 'MenuPage'));
		} else {
			add_action('admin_menu', array(&$this, 'MenuPage'));
		}
		
	add_filter('plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );
    }



/******************************************ADMIN SETTINGS PAGE BEGIN******************************************/

    function MenuPage()
    {
	add_menu_page('Advanced noCaptcha reCaptcha', 'Advanced noCaptcha', 'manage_options', 'anr-admin-settings', array(&$this, 'admin_settings'),plugins_url( 'advanced-nocaptcha-recaptcha/images/advanced-nocaptcha-recaptcha.jpg' ));	
	
	add_submenu_page('anr-admin-settings', 'Advanced noCaptcha reCaptcha - ' .__('Settings','anr'), __('Settings','anr'), 'manage_options', 'anr-admin-settings', array(&$this, 'admin_settings'));
	
	add_submenu_page('anr-admin-settings', 'Advanced noCaptcha reCaptcha - ' .__('Instruction','fepcf'), __('Instruction','fepcf'), 'manage_options', 'anr-instruction', array(&$this, "InstructionPage"));
	
    }
	

    function admin_settings()
    {
	  $token = wp_create_nonce( 'anr-admin-settings' );
	  $url = 'https://shamimbiplob.wordpress.com/contact-us/';
	  $ReviewURL = 'https://wordpress.org/support/view/plugin-reviews/advanced-nocaptcha-recaptcha';
	  echo "<style>
			input[type='text'], textarea, select {
				width: 100%;
			}
		</style>";
	  $languages = array(
							__( 'Auto Detect', 'anr' )         	=> '',
							__( 'Arabic', 'anr' )              	=> 'ar',
							__( 'Bulgarian', 'anr' )           	=> 'bg',
							__( 'Catalan', 'anr' )             	=> 'ca',
							__( 'Chinese (Simplified)', 'anr' )	=> 'zh-CN',
							__( 'Chinese (Traditional)', 'anr' ) => 'zh-TW',
							__( 'Croatian', 'anr' )           	=> 'hr',
							__( 'Czech', 'anr' )             	=> 'cs',
							__( 'Danish', 'anr' )             	=> 'da',
							__( 'Dutch', 'anr' )              	=> 'nl',
							__( 'English (UK)', 'anr' )         => 'en-GB',
							__( 'English (US)', 'anr' )         => 'en',
							__( 'Filipino', 'anr' )				=> 'fil',
							__( 'Finnish', 'anr' ) 				=> 'fi',
							__( 'French', 'anr' )           	=> 'fr',
							__( 'French (Canadian)', 'anr' )   	=> 'fr-CA',
							__( 'German', 'anr' )   			=> 'de',
							__( 'German (Austria)', 'anr' )		=> 'de-AT',
							__( 'German (Switzerland)', 'anr' ) => 'de-CH',
							__( 'Greek', 'anr' )           		=> 'el',
							__( 'Hebrew', 'anr' )             	=> 'iw',
							__( 'Hindi', 'anr' )             	=> 'hi',
							__( 'Hungarain', 'anr' )            => 'hu',
							__( 'Indonesian', 'anr' )         	=> 'id',
							__( 'Italian', 'anr' )         		=> 'it',
							__( 'Japanese', 'anr' )				=> 'ja',
							__( 'Korean', 'anr' ) 				=> 'ko',
							__( 'Latvian', 'anr' )           	=> 'lv',
							__( 'Lithuanian', 'anr' )   		=> 'lt',
							__( 'Norwegian', 'anr' )   			=> 'no',
							__( 'Persian', 'anr' )           	=> 'fa',
							__( 'Polish', 'anr' )   			=> 'pl',
							__( 'Portuguese', 'anr' )   		=> 'pt',
							__( 'Portuguese (Brazil)', 'anr' )  => 'pt-BR',
							__( 'Portuguese (Portugal)', 'anr' )=> 'pt-PT',
							__( 'Romanian', 'anr' )         	=> 'ro',
							__( 'Russian', 'anr' )         		=> 'ru',
							__( 'Serbian', 'anr' )				=> 'sr',
							__( 'Slovak', 'anr' ) 				=> 'sk',
							__( 'Slovenian', 'anr' )           	=> 'sl',
							__( 'Spanish', 'anr' )   			=> 'es',
							__( 'Spanish (Latin America)', 'anr' )=> 'es-419',
							__( 'Swedish', 'anr' )           	=> 'sv',
							__( 'Thai', 'anr' )   				=> 'th',
							__( 'Turkish', 'anr' )   			=> 'tr',
							__( 'Ukrainian', 'anr' )   			=> 'uk',
							__( 'Vietnamese', 'anr' )   		=> 'vi'
							
							);
							
		$locations = array(	 
							__( 'Login Form', 'anr' )   				=> 'login',
							__( 'Registration Form', 'anr' )   			=> 'registration',
							__( 'Multisite User Signup Form', 'anr' )   => 'ms_user_signup',
							__( 'Lost Password Form', 'anr' )   		=> 'lost_password',
							__( 'Reset Password Form', 'anr' )  		=> 'reset_password',
							__( 'Comment Form', 'anr' )   				=> 'comment',
							__( 'bbPress New topic', 'anr' )   			=> 'bb_new',
							__( 'bbPress reply to topic', 'anr' )		=> 'bb_reply',
									
							);
									
	  
	  if(isset($_POST['anr-admin-settings-submit'])){ 
		$errors = $this->admin_settings_action();
		if(count($errors->get_error_messages())>0){
			echo anr_error($errors);
		}
		else{
		echo'<div id="message" class="updated fade">' .__("Options successfully saved.", 'anr'). ' </div>';}}
		echo "<div id='poststuff'>

		<div id='post-body' class='metabox-holder columns-2'>

		<!-- main content -->
		<div id='post-body-content'>
		<div class='postbox'><div class='inside'>
	  	  <h2>".__("Advanced noCaptcha reCaptcha Settings", 'anr')."</h2>
		  <h5>".sprintf(__("If you like this plugin please <a href='%s' target='_blank'>Review in Wordpress.org</a> and give 5 star", 'anr'),esc_url($ReviewURL))."</h5>
          <form method='post' action=''>
          <table>
          <thead>
          <tr><th width = '50%'>".__("Setting", 'anr')."</th><th width = '50%'>".__("Value", 'anr')."</th></tr>
          </thead>
          <tr><td>".__("Site Key", 'anr')."<br/><small><a href='https://www.google.com/recaptcha/admin' target='_blank'>Get From Google</a></small></td><td><input type='text' size = '40' name='site_key' value='".anr_get_option('site_key')."' /></td></tr>
		  <tr><td>".__("Secret key", 'anr')."<br/><small><a href='https://www.google.com/recaptcha/admin' target='_blank'>Get From Google</a></small></td><td><input type='text' size = '40' name='secret_key' value='".anr_get_option('secret_key')."' /></td></tr>
		  
		  <tr><td>".__("Language", 'anr')."</td><td><select name='language'>";
		  
		  foreach ( $languages as $language => $code ) {
		  
		  echo "<option value='$code' ".selected(anr_get_option('language'), $code,false).">$language</option>";
		  
		  }
		  
		  echo "</select></td></tr>
		  <tr><td>".__("Theme", 'anr')."</td><td><select name='theme'>
		  
		  <option value='light' ".selected(anr_get_option('theme'), 'light',false).">Light</option>
		  <option value='dark' ".selected(anr_get_option('theme'), 'dark',false).">Dark</option>
		  
		  </select></td></tr>
		  <tr><td>".__("Size", 'anr')."</td><td><select name='size'>
		  
		  <option value='normal' ".selected(anr_get_option('size'), 'normal',false).">Normal</option>
		  <option value='compact' ".selected(anr_get_option('size'), 'compact',false).">Compact</option>
		  
		  </select></td></tr>
		  <tr><td>".__("Error Message", 'anr')."</td><td><input type='text' size = '40' name='error_message' value='".anr_get_option('error_message', '<strong>ERROR</strong>: Please solve Captcha correctly.')."' /></td></tr>
		  
		  <tr><td>".__("Show Captcha on", 'anr')."</td><td>";
		  
		  foreach ( $locations as $location => $slug ) {
		  
		  echo "<ul colspan='2'><label><input type='checkbox' name='$slug' value='1' ".checked(anr_get_option($slug), '1', false)." /> $location</label></ul>";
		  
		  }
		  if ( function_exists('fepcf_plugin_activate'))
		  echo "<ul colspan='2'><label><input type='checkbox' name='fep_contact_form' value='1' ".checked(anr_get_option('fep_contact_form'), '1', false)." /> FEP Contact Form</label></ul>";
		  else
		  echo "<ul colspan='2'><label><input type='checkbox' name='fep_contact_form' disabled value='1' ".checked(anr_get_option('fep_contact_form'), '1', false)." /> FEP Contact Form (is not installed) <a href='https://wordpress.org/plugins/fep-contact-form/' target='_blank'>Install Now</a></label></ul>";
		  
		  echo "<ul colspan='2'> For other forms see <a href='".esc_url(admin_url( 'admin.php?page=anr-instruction' ))."'>Instruction</a></ul>";
		  echo "</td></tr>";
		  
		  do_action('anr_admin_setting_form');
		  
		  echo "<tr><td colspan='2'><label><input type='checkbox' name='loggedin_hide' value='1' ".checked(anr_get_option('loggedin_hide'), '1', false)." /> ".__("Hide Captcha for logged in users?", 'anr')."</label></td></tr>
		  <tr><td colspan='2'><label><input type='checkbox' name='remove_css' value='1' ".checked(anr_get_option('remove_css'), '1', false)." /> ".__("Remove this plugin's css from login page?", 'anr')."<br/><small>".__("This css increase login page width to adjust with Captcha width.", 'anr')."</small></label></td></tr>
		  <tr><td colspan='2'><label><input type='checkbox' name='no_js' value='1' ".checked(anr_get_option('no_js'), '1', false)." /> ".__("Show captcha if javascript disabled?", 'anr')."<br/><small>".__("If JavaScript is a requirement for your site, we advise that you do NOT check this.", 'anr')."</small></label></td></tr>
          <tr><td colspan='2'><span><input class='button-primary' type='submit' name='anr-admin-settings-submit' value='".__("Save Options", 'anr')."' /></span></td><td><input type='hidden' name='token' value='$token' /></td></tr>
          </table>
		  </form>
		  <ul>".sprintf(__("For paid support pleasse visit <a href='%s' target='_blank'>Advanced noCaptcha reCaptcha</a>", 'anr'),esc_url($url))."</ul>
          </div></div></div>
		  ". $this->anr_admin_sidebar(). "
		  </div></div>";
		  }

function anr_admin_sidebar()
	{
		return '<div id="postbox-container-1" class="postbox-container">


				<div class="postbox">
					<h3 class="hndle" style="text-align: center;">
						<span>'. __( "Plugin Author", "anr" ). '</span>
					</h3>

					<div class="inside">
						<div style="text-align: center; margin: auto">
						<strong>Shamim Hasan</strong><br />
						Know php, MySql, css, javascript, html. Expert in WordPress. <br /><br />
								
						You can hire for plugin customization, build custom plugin or any kind of wordpress job via <br> <a
								href="https://shamimbiplob.wordpress.com/contact-us/"><strong>Contact Form</strong></a>
					</div>
				</div>
			</div>
				</div>';
	}
		

    function admin_settings_action()
    {
      if (isset($_POST['anr-admin-settings-submit']))
      {
	  $errors = new WP_Error();
	  $options = $_POST;
	  
	  if( !current_user_can('manage_options'))
	  $errors->add('noPermission', __('No Permission!', 'anr'));
	  
	  
	  if ( !wp_verify_nonce($options['token'], 'anr-admin-settings'))
			$errors->add('invalidToken', __('Sorry, your nonce did not verify!', 'anr'));
	  
	  $options = apply_filters('anr_filter_admin_setting_before_save', $options, $errors);
	  //var_dump($options);
		
		if (count($errors->get_error_codes())==0){
			if ( is_multisite() ) {
				$same_settings = apply_filters( 'anr_same_settings_for_all_sites', false );
			} else {
				$same_settings = false;
			}
			if ( $same_settings ) {
				update_site_option('anr_admin_options', $options);
			} else {
				update_option('anr_admin_options', $options);
			}
        }
		return $errors;
      }
      return false;
    }
	
	function InstructionPage()
	{
	$url = 'https://shamimbiplob.wordpress.com/contact-us/';
	echo '<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

		<!-- main content -->
		<div id="post-body-content">';
		
      echo 	"<div class='postbox'><div class='inside'>
          <h2>".__("Advanced noCaptcha reCaptcha Setup Instruction", 'anr')."</h2>
          <p><ul>
		  <li>".sprintf(__("Get your site key and secret key from <a href='%s' target='_blank'>GOOGLE</a> if you do not have already.", 'anr'),esc_url('https://www.google.com/recaptcha/admin'))."</li>
		  <li>".__("Goto SETTINGS page of this plugin and set up as you need. and ENJOY...", 'anr')."</li><br/>
		  <h3>".__("Implement noCaptcha in Contact Form 7", 'anr')."</h3><br />
          <li>".__("To show noCaptcha use ", 'anr')."<code>[anr_nocaptcha g-recaptcha-response]</code></li><br />
		  <h3>".__("If you want to implement noCaptcha in any other custom form", 'anr')."</h3><br />
          <li>".__("To show form field use ", 'anr')."<code>anr_captcha_form_field()</code></li>
		  <li>".__("To verify use ", 'anr')."<code>anr_verify_captcha()</code> it will return true on success otherwise false</li><br />
		  <li>".sprintf(__("For paid support pleasse visit <a href='%s' target='_blank'>Advanced noCaptcha reCaptcha</a>", 'anr'),esc_url($url))."</li>
          </ul></p></div></div></div>
		  ". $this->anr_admin_sidebar(). "
		  </div></div>";
		  }
	
	
function add_settings_link( $links, $file ) {
	//add settings link in plugins page
	$plugin_file = 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php';
	if ( $file == $plugin_file ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=anr-admin-settings' ) . '">' .__( 'Settings', 'anr' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}
/******************************************ADMIN SETTINGS PAGE END******************************************/


  } //END CLASS
} //ENDIF

add_action('wp_loaded', array(anr_admin_class::init(), 'actions_filters'));
?>