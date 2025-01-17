<?php
/**
* Plugin Name: Passwordless Login
* Plugin URI: https://www.cozmoslabs.com
* Description: Shortcode based login form. Enter an email/username and get link via email that will automatically log you in.
* Version: 1.1.3
* Author: Cozmoslabs, sareiodata
* Author URI: https://www.cozmoslabs.com
* License: GPL2
* Text Domain: passwordless-login
* Domain Path: /languages
*/
/* Copyright Cozmoslabs.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

// Start writing code after this line!


/**
 * Definitions
 *
 *
 */
define( 'PASSWORDLESS_LOGIN_VERSION', '1.1.3' );
define( 'WPA_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'WPA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Function that initiates the plugin text domain
 *
 * @since v.1.0.6
 *
 * @return void
 */
function wpa_load_plugin_textdomain(){
    load_plugin_textdomain( 'passwordless-login', false, WPA_PLUGIN_URL . '/languages/' );
}
add_action('init', 'wpa_load_plugin_textdomain');


/**
 * Function that creates the "Basic Information" submenu page
 *
 * @since v.2.0
 *
 * @return void
 */
function wpa_register_basic_info_submenu_page() {
	add_submenu_page( 'users.php', __( 'Passwordless Login', 'passwordless-login' ), __( 'Passwordless Login', 'passwordless-login' ), 'manage_options', 'passwordless-login', 'wpa_basic_info_content' );
}
add_action( 'admin_menu', 'wpa_register_basic_info_submenu_page', 2 );

/**
 * Function that adds content to the "Passwordless Auth" submenu page
 *
 * @since v.1.0
 *
 * @return string
 */
function wpa_basic_info_content() {
?>
	<div class="wrap wpa-wrap wpa-info-wrap">
		<div class="wpa-badge <?php echo PASSWORDLESS_LOGIN_VERSION; ?>"><?php printf( __( 'Version %s', 'passwordless-login' ), PASSWORDLESS_LOGIN_VERSION ); ?></div>
		<h1><?php printf( __( '<strong>Passwordless Login</strong> <small>v.</small>%s', 'passwordless-login' ), PASSWORDLESS_LOGIN_VERSION ); ?></h1>
		<p class="wpa-info-text"><?php printf( __( 'A front-end login form without a password.', 'passwordless-login' ) ); ?></p>
		<p><strong style="font-size: 30px; vertical-align: middle; color:#d54e21;"><?php echo get_option('wpa_total_logins', '0'); ?></strong> <?php _e('successful logins without passwords.', 'passwordless-login'); ?></p>
		<hr />
		<h2 class="wpa-callout"><?php _e( 'One time password for WordPress', 'passwordless-login' ); ?></h2>
		<div class="wpa-row wpa-2-col">
			<div>
				<h3><?php _e( '[passwordless-login] shortcode', 'passwordless-login' ); ?></h3>
				<p><?php _e( 'Just place <strong class="nowrap">[passwordless-login]</strong> shortcode in a page or a widget and you\'re good to go.', 'passwordless-login' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'An alternative to passwords', 'passwordless-login'  ); ?></h3>
				<p><?php _e( 'Passwordless Authentication <strong>dose not</strong> replace the default login functionality in WordPress. Instead you can have the two work in parallel.', 'passwordless-login' ); ?></p>
				<p><?php _e( 'Join the discussion here: <a href="http://www.cozmoslabs.com/?p=31550&utm_source=wpbackend&utm_medium=link&utm_content=link&utm_campaign=passwordless">WordPress Passwordless Authentication</a>', 'passwordless-login' ); ?></p>
			</div>
		</div>
		<hr/>
		<div>
			<h3><?php _e( 'Take control of the login and registration process with Profile Builder', 'passwordless-login' );?></h3>
			<p><?php _e( 'Improve upon Passwordless Authentication using the free <a href="https://wordpress.org/plugins/profile-builder/">Profile Builder</a> plugin:', 'passwordless-login' ); ?></p>
			<div class="wpa-row wpa-3-col">
				<div><p><?php _e('Front-End registration, edit profile and login forms.', 'passwordless-login'); ?></p></div>
				<div><p><?php _e('Drag and drop to reorder / remove default user profile fields.', 'passwordless-login'); ?></p></div>
				<div><p><?php _e('Allow users to log in with their username or email.', 'passwordless-login'); ?></p></div>
				<div><p><?php _e('Enforce minimum password length and minimum password strength.', 'passwordless-login'); ?></p></div>
			</div>
			<p><a href="https://wordpress.org/plugins/profile-builder/" class="button button-primary button-large"><?php _e( 'Learn More About Profile Builder', 'passwordless-login' ); ?></a></p>
		</div>
	</div>
<?php
}


/**
 * Add scripts and styles to the back-end
 *
 * @since v.1.0
 *
 * @return void
 */
function wpa_print_script( $hook ){
	if ( ( $hook == 'users_page_passwordless-login' ) ){
		wp_enqueue_style( 'wpa-back-end-style', WPA_PLUGIN_URL . 'assets/style-back-end.css', false, PASSWORDLESS_LOGIN_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'wpa_print_script' );

/**
 * Add scripts and styles to the front-end
 *
 * @since v.1.0
 *
 * @return void
 */
function wpa_add_plugin_stylesheet() {
	if (  file_exists( WPA_PLUGIN_DIR . '/assets/style-front-end.css' )  ){
		wp_register_style( 'wpa_stylesheet', WPA_PLUGIN_URL . 'assets/style-front-end.css' );
		wp_enqueue_style( 'wpa_stylesheet' );
	}
}
add_action( 'wp_print_styles', 'wpa_add_plugin_stylesheet' );

/**
 * Shortcode for the passwordless login form
 *
 * @since v.1.0
 *
 * @return html
 */
function wpa_front_end_login(){
	ob_start();
	$account        = ( isset( $_POST['user_email_username']) ) ? $account = sanitize_text_field( $_POST['user_email_username'] ) : false;
	$error_token    = ( isset( $_GET['wpa_error_token']) ) ? $error_token  = sanitize_key( $_GET['wpa_error_token'] ) : false;
	$adminapp_error = ( isset( $_GET['wpa_adminapp_error']) ) ? sanitize_key( $_GET['wpa_adminapp_error'] ) : false;

	$sent_link = get_option('wpa_login_request_error');

	if( $account && !is_wp_error($sent_link) ){
		echo '<p class="wpa-box wpa-success">'. apply_filters('wpa_success_link_msg', __('Please check your email. You will soon receive an email with a login link.', 'passwordless-login') ) .'</p>';
	} elseif ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		echo '<p class="wpa-box wpa-alert">'.apply_filters('wpa_success_login_msg', sprintf(__( 'You are currently logged in as %1$s. %2$s', 'passwordless-login' ), '<a href="'.esc_url( get_author_posts_url( $current_user->ID ) ).'" title="'.esc_attr( $current_user->display_name ).'">'.esc_html( $current_user->display_name ).'</a>', '<a href="'.esc_url( wp_logout_url( $redirectTo = wpa_curpageurl() ) ).'" title="'.__( 'Log out of this account', 'passwordless-login' ).'">'. __( 'Log out', 'passwordless-login').' &raquo;</a>' ) ) . '</p><!-- .alert-->';
	} else {
		if ( is_wp_error($sent_link) ){
			echo '<p class="wpa-box wpa-error">' . esc_html( apply_filters( 'wpa_error', $sent_link->get_error_message() ) ) . '</p>';
		}
		if( $error_token ) {
			echo '<p class="wpa-box wpa-error">' . apply_filters( 'wpa_invalid_token_error', __('Your token has probably expired. Please try again.', 'passwordless-login') ) . '</p>';
		}
        if( $adminapp_error ) {//admin approval compatibility
            echo '<p class="wpa-box wpa-error">' . apply_filters( 'wpa_admin_approval_error', __('Your account needs to be approved by an admin before you can log-in.', 'passwordless-login') ) . '</p>';
        }
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		//Setting up the label for the password request form based on the Allows Users to Login With Profile Builder Option
		if (is_plugin_active('profile-builder-pro/index.php') || is_plugin_active('profile-builder/index.php') || is_plugin_active('profile-builder-hobbyist/index.php')) {
			$wppb_general_options = get_option('wppb_general_settings');

			if ($wppb_general_options !== false) {
				if ($wppb_general_options['loginWith'] == 'email')
					$label = __('Login with email', 'passwordless-login') . '<br>';
				else if ($wppb_general_options['loginWith'] == 'username')
					$label = __('Login with username', 'passwordless-login') . '<br>';
				else
					$label = __('Login with email or username', 'passwordless-login');
			}
		}
		else
			$label = __('Login with email or username', 'passwordless-login');
		?>
	<form name="wpaloginform" id="wpaloginform" action="" method="post">
		<p>
			<label for="user_email_username"><?php echo esc_html( apply_filters('wpa_change_form_label', $label ) ); ?></label>
			<input type="text" name="user_email_username" id="user_email_username" class="input" value="<?php echo esc_attr( $account ); ?>" size="25" />
			<input type="submit" name="wpa-submit" id="wpa-submit" class="button-primary" value="<?php esc_attr_e('Log In', 'passwordless-login'); ?>" />
		</p>
		<?php do_action('wpa_login_form'); ?>
		<?php wp_nonce_field( 'wpa_passwordless_login_request', 'nonce', false ) ?>

	</form>
<?php
	}

    // remove errors saved when sending email notification failed in wpa_send_login_request()
    delete_option( 'wpa_login_request_error' );

	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode( 'passwordless-login', 'wpa_front_end_login' );

add_filter('widget_text', 'do_shortcode');

/**
 * Checks to see if an account is valid. Either email or username
 *
 * @since v.1.0
 *
 * @return bool / WP_Error
 */
function wpa_valid_account( $account ){
	if( is_email( $account ) ) {
		$account = sanitize_email( $account );
	} else {
		$account = sanitize_user( $account );
	}

	if( is_email( $account ) && email_exists( $account ) ) {
		return $account;
	}

	if( ! is_email( $account ) && username_exists( $account ) ) {
		$user = get_user_by( 'login', $account );
		if( $user ) {
			return $user->data->user_email;
		}
	}

	return new WP_Error( 'invalid_account', __( 'The username or email you provided does not exist. Please try again.', 'passwordless-login' ) );
}

/**
 * Send login request email to user
 *
 */
function wpa_send_login_request() {

    $nonce = ( isset( $_POST['nonce']) ) ? sanitize_key( $_POST['nonce'] ) : false;

    if ( wp_verify_nonce( $nonce, 'wpa_passwordless_login_request' ) ) {

        $user_login = ( isset( $_POST['user_email_username']) ) ? sanitize_text_field( $_POST['user_email_username'] ) : false;
        $sent_link = wpa_send_link( $user_login, $nonce );

        if ( $sent_link )
            update_option( 'wpa_login_request_error', $sent_link ); // save returned errors if the email notification could not be sent

    }
}
add_action( 'init', 'wpa_send_login_request' );

/**
 * Sends an email with the unique login link.
 *
 * @since v.1.0
 *
 * @return bool / WP_Error
 */
function wpa_send_link( $email_account = false, $nonce = false ){
	if ( $email_account  == false ){
		return false;
	}
	$valid_email = wpa_valid_account( $email_account  );
	$valid_email = apply_filters('wpa_email_verify_login', $valid_email);
	$errors = new WP_Error;
	if (is_wp_error($valid_email)){
		$errors->add('invalid_account', $valid_email->get_error_message());
	} else{
		$blog_name = get_bloginfo( 'name' );
		$blog_name = esc_attr( $blog_name );

		//Filters to change the content type of the email
		add_filter('wp_mail_content_type', function(){ return "text/html"; } );

		$unique_url = wpa_generate_url( $valid_email , $nonce );
		$subject = apply_filters('wpa_email_subject', sprintf(__("Login at %s", 'passwordless-login'), $blog_name));
		$message = apply_filters('wpa_email_message', sprintf(__('Hello ! <br><br>Login at %s by visiting this url: <a href="%s" target="_blank">%s</a>', 'passwordless-login'),$blog_name, esc_url($unique_url), esc_url($unique_url)),$unique_url, $valid_email);
		$headers = apply_filters('wpa_email_headers', '', $unique_url, $valid_email);
		$sent_mail = wp_mail( $valid_email, $subject, $message, $headers );

		if ( !$sent_mail ){
			$errors->add('email_not_sent', __('There was a problem sending your email. Please try again or contact an admin.', 'passwordless-login'));
		}
	}
	$error_codes = $errors->get_error_codes();

	if (empty( $error_codes  )){
		return false;
	}else{
		return $errors;
	}
}

/**
 * Generates unique URL based on UID and nonce
 *
 * @since v.1.0
 *
 * @return string
 */
function wpa_generate_url( $email = false, $nonce = false ){
	if ( $email  == false ){
		return false;
	}
	/* get user id */
	$user = get_user_by( 'email', $email );
	$token = wpa_create_onetime_token( 'wpa_'.$user->ID, $user->ID  );

	$arr_params = array( 'wpa_error_token', 'uid', 'token', 'nonce' );
	$url = remove_query_arg( $arr_params, wpa_curpageurl() );

    $url_params = array('uid' => $user->ID, 'token' => $token, 'nonce' => $nonce);
    $url = add_query_arg($url_params, $url);

	return $url;
}

/**
 * Automatically logs in a user with the correct nonce
 *
 * @since v.1.0
 *
 * @return string
 */
add_action( 'init', 'wpa_autologin_via_url' );
function wpa_autologin_via_url(){

    if( $_SERVER['REQUEST_METHOD'] === "HEAD" ){
        // Redirect to HomePage when REQUEST_METHOD is set to HEAD (avoid issues regarding antivirus Link Protection)
        wp_redirect( 'userprivatefiles', 301 );
        exit;
    }

	if( isset( $_GET['token'] ) && isset( $_GET['uid'] ) && isset( $_GET['nonce'] ) ){
		$uid   = sanitize_key( $_GET['uid'] );
		$token = sanitize_key( $_REQUEST['token'] );
		$nonce = sanitize_key( $_REQUEST['nonce'] );

		$hash_meta            = get_user_meta( $uid, 'wpa_' . $uid, true);
		$hash_meta_expiration = get_user_meta( $uid, 'wpa_' . $uid . '_expiration', true);
		$arr_params           = array( 'uid', 'token', 'nonce' );
		$current_page_url     = remove_query_arg( $arr_params, wpa_curpageurl() );

		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		$wp_hasher = new PasswordHash(8, TRUE);
		$time      = time();

		$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');//profile builder settings are required for admin approval compatibility

		if ( ! $wp_hasher->CheckPassword($token . $hash_meta_expiration, $hash_meta) || $hash_meta_expiration < $time || ! wp_verify_nonce( $nonce, 'wpa_passwordless_login_request' ) ){
			wp_redirect( $current_page_url . '?wpa_error_token=true' );
			exit;
		}else if ( defined('PROFILE_BUILDER_VERSION') && $wppb_generalSettings != 'not_found' && !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'yes' && wp_get_object_terms( $uid, 'user_status' ) ){//admin approval compatibility
            wp_redirect( $current_page_url . '?wpa_adminapp_error=true' );
            exit;
        }
		else {
			wp_set_auth_cookie( $uid );
			delete_user_meta($uid, 'wpa_' . $uid );
			delete_user_meta($uid, 'wpa_' . $uid . '_expiration');

			$total_logins = get_option( 'wpa_total_logins', 0);
			update_option( 'wpa_total_logins', $total_logins + 1);

			if ( function_exists('wppb_custom_redirect_url') ){
				$wppb_custom_redirects_url = wppb_custom_redirect_url( 'after_login', $current_page_url );
			}

			$redirect_url = 'userprivatefiles';

			wp_redirect( apply_filters('wpa_after_login_redirect', $redirect_url ) );
			exit;
		}

	}
}

/**
 * Create a nonce like token that you only use once based on transients
 *
 *
 * @since v.1.0
 *
 * @return string
 */
function wpa_create_onetime_token( $action = -1, $user_id = 0 ) {
	$time = time();

	// random salt
	$key = wp_generate_password( 20, false );

	require_once( ABSPATH . 'wp-includes/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);
	$string = $key . $action . $time;

	// we're sending this to the user
	$token  = wp_hash( $string );
	$expiration = apply_filters('wpa_change_link_expiration', $time + 60*10);
	$expiration_action = $action . '_expiration';

	// we're storing a combination of token and expiration
	$stored_hash = $wp_hasher->HashPassword( $token . $expiration );

	update_user_meta( $user_id, $action , $stored_hash ); // adjust the lifetime of the token. Currently 10 min.
	update_user_meta( $user_id, $expiration_action , $expiration );
	return $token;
}

/**
 * Returns the current page URL
 *
 * @since v.1.0
 *
 * @return string
 */
function wpa_curpageurl() {
    $req_uri = $_SERVER['REQUEST_URI'];

	$parsed_url = parse_url( home_url(), PHP_URL_PATH );

    if( !empty( $parsed_url ) )
        $home_path = trim( $parsed_url, '/' );
    else
        $home_path = $parsed_url;

    if( $home_path === null || $home_path === false )
        $home_path = '';

    $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

    // Trim path info from the end and the leading home path from the front.
    $req_uri = ltrim($req_uri, '/');
    $req_uri = preg_replace( $home_path_regex, '', $req_uri );
    $req_uri = trim(home_url(), '/') . '/' . ltrim( $req_uri, '/' );

    return $req_uri;
}


/**
 * Add notices on plugin activation.
 *
 * @since v.1.0
 *
 * @return string
 */
add_action( 'admin_init', 'wpa_admin_general_notices', 9 );
function wpa_admin_general_notices(){
	include_once("inc/wpa.class.notices.php");
	$learn_more_notice = new WPA_Add_Notices(
		'wpa_learn_more',
		sprintf( __( '<p>Use [passwordless-login] shortcode in your pages or widgets. %1$sLearn more.%2$s  %3$sDismiss%4$s</p>', 'passwordless-login'), "<a href='users.php?page=passwordless-login&wpa_learn_more_dismiss_notification=0'>", "</a>", "<a href='". wp_nonce_url( add_query_arg( 'wpa_learn_more_dismiss_notification', '0' ), 'wpa_notice_dismiss' ) ."' class='wpa-dismiss-notification' style='float:right;margin-left:20px;'> ", "</a>" ),
		'updated',	'',	''
	);	
}
