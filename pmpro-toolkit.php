<?php
/**
 * Plugin Name: Paid Memberships Pro - Developer's Toolkit Add On
 * Plugin URI: https://www.paidmembershipspro.com/add-ons/pmpro-toolkit/
 * Description: Various tools to test and debug Paid Memberships Pro enabled websites.
 * Version: 0.8.1
 * Author: Paid Memberships Pro
 * Author URI: https://www.paidmembershipspro.com
 * Text Domain: pmpro-toolkit
*/

/*
* Globals
*/
global $pmprodev_options, $gateway;
$default_options = array(
	'expire_memberships' => '',
	'expiration_warnings' => '',
	'credit_card_expiring' => '',
	'ipn_debug' => '',
	'authnet_silent_post_debug' => '',
	'stripe_webhook_debug' => '',
	'ins_debug' => '',
	'redirect_email' => '',
	'checkout_debug_email' => '',
	'checkout_debug_when' => '',
	'view_as_enabled' => false
);

$pmprodev_options = get_option( 'pmprodev_options' );

if( empty( $pmprodev_options ) ) {
	$pmprodev_options = $default_options;
} else {
	$pmprodev_options = array_merge( $default_options, $pmprodev_options );
}

//intialize options in the DB
function pmprodev_init_options() {
	global $pmprodev_options, $default_options;
	if( ! $pmprodev_options || empty( $pmprodev_options ) ) {
		$pmprodev_options = $default_options;
		update_option( 'pmprodev_options', $pmprodev_options );
	}
}

add_action( 'admin_init', 'pmprodev_init_options' );

define( 'PMPRODEV_DIR', dirname( __FILE__ ) );
include_once PMPRODEV_DIR . '/classes/class-pmprodev-migration-assistant.php';

/*
* Gateway Debug Constants
*/

/**
 * Remove the cron jobs for expiration warnings and expiring credit cards if the options are set.
 *
 * @return void
 * @since TBD
 */
function pmprodev_gateway_debug_setup() {

	global $pmprodev_options;

	//unhook crons
	if( !empty($pmprodev_options['expire_memberships'] ) ) {
		remove_action("pmpro_cron_expire_memberships", "pmpro_cron_expire_memberships");
	}
	if( !empty($pmprodev_options['expiration_warnings'] ) )	{
		remove_action( "pmpro_cron_expiration_warnings", "pmpro_cron_expiration_warnings" );
	}
	if( !empty( $pmprodev_options['credit_card_expiring'] ) ) {
		remove_action( "pmpro_cron_credit_card_expiring_warnings", "pmpro_cron_credit_card_expiring_warnings" );
	}	
}
add_action('init', 'pmprodev_gateway_debug_setup');

/**
 * If there is a redirect email set, redirect all PMPro emails to that email.
 *
 * @param string $recipient the email recipient
 * @param object $email the email object
 * @return string $recipient the email recipient
 * @since TBD
 *
 */
function pmprodev_redirect_emails( $recipient, $email ) {

	global $pmprodev_options;

	if( !empty($pmprodev_options['redirect_email'] ) ) {
		$recipient = $pmprodev_options['redirect_email'];
	}

	return $recipient;
}
add_filter( 'pmpro_email_recipient', 'pmprodev_redirect_emails', 10, 2 );

/**
 * Send debug email every time checkout page is hit. 
 *
 * @param mixed $filter_contents to not break the wp_redirect filter.
 * @return mixed $filter_contents to not break the wp_redirect filter.
 * @since TBD
 */
function pmprodev_checkout_debug_email( $filter_contents = null ) {

	global $pmprodev_options, $current_user, $wpdb, $pmpro_msg, $pmpro_msgt;

	// Ignore the dashboard, AJAX, and webhooks.
	if ( is_admin() || defined( 'DOING_AJAX' ) || pmpro_doing_webhook() ) {
		return $filter_contents;
	}
	
	// Avoid issues if we're redirecting too early before pmpro_is_checkout will work.
	if ( ! did_action('wp') ) {
		return $filter_contents;
	}

	// Make sure this is the checkout page.
	if( ! function_exists( 'pmpro_is_checkout' ) || ! pmpro_is_checkout() ) {
		return $filter_contents;
	}
	
	// Make sure they have turned this on.
	if( empty( $pmprodev_options['checkout_debug_when'] ) ) {
		return $filter_contents;
	}

	// Make sure we have an email to use.
	if( empty( $pmprodev_options['checkout_debug_email'] ) ) {
		return $filter_contents;
	}

	// Make sure the checkout form was submitted if using that option.
	if ( $pmprodev_options['checkout_debug_when'] === 'on_submit' && empty( $_REQUEST['submit-checkout'] ) ) {
		return $filter_contents;
	}    

	// Make sure there is an error if using that option.
	if ( $pmprodev_options['checkout_debug_when'] === 'on_error' && ( empty( $pmpro_msgt ) || $pmpro_msgt != 'pmpro_error' ) ) {
		return $filter_contents;
	}

	// We're going to send an email. Make sure we don't send more than one.
	$pmprodev_options['checkout_debug_when'] = false;

	// Get some values.
	$level = pmpro_getLevelAtCheckout();
	$email = new PMProEmail();
	if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
		$http = 'https://';
	} else {
		$http = 'http://';
	}

	// Remove password data.
	$user_pass_bu = $current_user->user_pass;
	$current_user->user_pass = '';    
	if ( isset( $_REQUEST['password'] ) ) {
		$password_bu = $_REQUEST['password'];
		$_REQUEST['password'] = '';
	}    
	if ( isset( $_REQUEST['password2'] ) ) {
		$password2_bu = $_REQUEST['password2'];
		$_REQUEST['password2'] = '';
	}

	// Set up the email.
	$email->subject = sprintf( '%s Checkout Page Debug Log', get_bloginfo( 'name' ) );
	$email->email = $pmprodev_options['checkout_debug_email'];
	$email->template = 'checkout_debug';
	$email->body = file_get_contents( plugin_dir_path(__FILE__) . '/email/checkout_debug.html' );
	$email->data = array(
		'sitename' => get_bloginfo( 'sitename' ),
		'checkout_url' => $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'submit' => ( empty( $_REQUEST['submit-checkout']) ? 'no' : 'yes' ),
		'level' => print_r( $level, true ),
		'user' => print_r( $current_user->data, true ),
		'request' => print_r( $_REQUEST, true ),
		'message_type' => ( empty( $pmpro_msgt ) ? 'N/A' : $pmpro_msgt . '|' ),
		'message' => $pmpro_msg
	);
	
	// Add passwords back, just in case.
	if ( isset( $user_pass_bu ) ) {
		$current_user->user_pass = $user_pass_bu;
	}
	if ( isset( $password_bu ) ) {
		$_REQUEST['password'] = $password_bu;
	}
	if ( isset( $user_pass_bu ) ) {
		$_REQUEST['password2'] = $password2_bu;
	}

	$order = new MemberOrder();
	$order->getLastMemberOrder( $current_user->user_id );

	if(!empty( $order ) ){
		$email->data['order'] = print_r( $order, true );
	}

	$email->sendEmail();

	return $filter_contents;
}
add_action( 'template_redirect', 'pmprodev_checkout_debug_email', 2 );
add_filter( 'wp_redirect', 'pmprodev_checkout_debug_email', 100 );
add_action( 'pmpro_membership_post_membership_expiry', 'pmprodev_checkout_debug_email' );
add_action( 'shutdown', 'pmprodev_checkout_debug_email' );


/**
 * Creates  a cookie based on query string parameters.
 *
 * @return void
 * @since TBD 
 */
function pmprodev_view_as_init() {

	global $current_user, $pmprodev_options;

	if( !empty( $_REQUEST['pmprodev_view_as'] ) ) {
		$view_as_level_ids = $_REQUEST['pmprodev_view_as'];
	}

	$membership_level_capability = apply_filters( 'pmpro_edit_member_capability', 'manage_options' );

	if( !empty( $view_as_level_ids ) && !empty( $pmprodev_options['view_as_enabled'] )
		&& current_user_can( $membership_level_capability ) ) {

		//are we resetting the filter?
		if( $view_as_level_ids == 'r' ) {
			setcookie('pmprodev_view_as', '', 0);
		} else {
			$options = array();
			setcookie( 'pmprodev_view_as', $view_as_level_ids, $options );
		}
	}
}
add_action('init', 'pmprodev_view_as_init');

/**
 * Override the pmpro_has_membership_access function to allow for viewing as other levels.
 * 
 * @param bool $hasaccess true if the user has access, false if not
 * @param WP_Post $post the post object
 * @param WP_User $user the WP_User object
 * @param array $levels the levels array
 * @return bool $hasaccess true if the user has access, false if not
 * @since TBD
 */
function pmprodev_view_as_access_filter( $hasaccess, $post, $user, $levels ) {

	global $pmprodev_options;

	if( !empty( $_COOKIE['pmprodev_view_as'] ) ) {
		$view_as_level_ids = $_COOKIE['pmprodev_view_as'];
	} else {
		$view_as_level_ids = NULL;
	}

	$membership_level_capability = apply_filters( 'pmpro_edit_member_capability', 'manage_options' );

	if( isset( $view_as_level_ids ) && current_user_can( $membership_level_capability ) ) {

		//default to false to override any real membership levels
		$hasaccess = false;

		//get level ids for this post
		$post_level_ids = array();
		foreach( $levels as $key=>$level ) {
			$post_level_ids[] = $level->id;
		}

		//get view as level ids from cookie
		$view_as_level_ids = explode( '-', $view_as_level_ids );

		foreach( $view_as_level_ids as $id ) {
			//return true when we find a match
			if( in_array( $id, $post_level_ids ) ) {
				$hasaccess = true;
			}
		}
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'pmprodev_view_as_access_filter', 10, 4 );

/**
 * Override the pmpro_has_membership_level function to allow for viewing as other levels.
 *
 * @param bool $return true if the user has the level, false if not
 * @param int $user_id the user ID
 * @param mixed $levels the levels to check
 * @return bool $return true if the user has the level, false if not
 * @since TBD
 * @todo figure out why this is running before cookie is set...
 */
function pmprodev_view_as_has_membership_level( $return, $user_id, $levels ) {

	global $pmprodev_options;

	if( !empty( $_COOKIE['pmprodev_view_as'] ) ) {
		$view_as_level_ids = $_COOKIE['pmprodev_view_as'];
	} else {
		$view_as_level_ids = NULL;
	}
		
	$membership_level_capability = apply_filters( 'pmpro_edit_member_capability', 'manage_options' );

	if( isset( $view_as_level_ids ) && current_user_can( $membership_level_capability ) ) {

		//if we're checking for "0"
		if( $levels == '0' && $view_as_level_ids == 'n' ) {
			return true;
		}

		//make levels array if it's not already
		if( !is_array( $levels ) ) {
			$levels = array( $levels );
		}

		//get view as level ids from cookie
		$view_as_level_ids = explode( '-', $view_as_level_ids );

		foreach( $view_as_level_ids as $id ) {
			if( in_array( $id, $levels ) ) {
				return true;
			}
		}

		//default to false to overrdide real levels
		return false;
	}

	return $return;
}
add_filter( 'pmpro_has_membership_level', 'pmprodev_view_as_has_membership_level', 10, 3 );

/**
 * Add settings page to the PMPro admin menu.
 *
 * @return void
 * @since TBD
 */
function pmprodev_admin_menu() {
	$pmprodev_menu_text = __( 'Toolkit', 'pmpro-toolkit' );
	add_submenu_page( 'pmpro-dashboard', $pmprodev_menu_text, $pmprodev_menu_text, 'manage_options',
		'pmpro-toolkit', 'pmprodev_settings_page' );
}

add_action('admin_menu', 'pmprodev_admin_menu');
add_action( 'admin_bar_menu', 'pmprodev_admin_menu_bar', 2000 );

/**
 * Add a menu item to the PMPro admin bar menu.
 *
 * @param WP_Admin_Bar $wp_admin_bar the WP_Admin_Bar object.
 * @return void
 * @since TBD
 */
function pmprodev_admin_menu_bar( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id' => 'pmprodev',
		'title' => 'PMPro Toolkit',
		'href' => admin_url( 'admin.php?page=pmpro-toolkit' ),
		'parent' => 'paid-memberships-pro',
		'meta' => array( 'class' => 'pmpro-dev' )
	) );
}

/**
 * Catch request and call export function.
 *
 * @return void
 * @since TBD
 */
function pmprodev_process_migration_export() {
	if ( ! empty( $_REQUEST['page'] ) && 'pmpro-toolkit' === $_REQUEST['page'] && ! empty( $_REQUEST['section'] )
	&& 'migration' === $_REQUEST['section'] && ! empty( $_REQUEST['pmprodev_export_options'] ) ) {
		PMProDev_Migration_Assistant::export( $_REQUEST['pmprodev_export_options'] );
	}
}
add_action( 'admin_init', 'pmprodev_process_migration_export' );

/**
 * Load the settings page.
 *
 * @return void
 * @since TBD	
 */
function pmprodev_settings_page() {
	require_once( plugin_dir_path( __FILE__ ) . '/adminpages/settings.php' );
}

/**
 * Load the text domain for translation.
 *
 * @return void
 * @since TBD
 */
function pmpro_toolkit_load_textdomain() {
	// get the locale
	$locale = apply_filters( 'plugin_locale', get_locale(), 'pmpro-toolkit' );
	$mofile = 'pmpro-toolkit-' . $locale . '.mo';

	// paths to local (plugin) and global (WP) language files
	$mofile_local  = plugin_dir_path( __FILE__ ) . '/languages/' . $mofile;
	$mofile_global = WP_LANG_DIR . '/pmpro/' . $mofile;

	// load global first
	load_textdomain( 'pmpro-toolkit', $mofile_global );

	// load local second
	load_textdomain( 'pmpro-toolkit', $mofile_local );
}
add_action( 'init', 'pmpro_toolkit_load_textdomain', 1 );

/**
 *  Add links to the plugin row meta.
 *
 * @param array $links the links array
 * @param string $file the file name
 */
function pmprodev_plugin_row_meta( $links, $file ) {
	if( strpos( $file, 'pmpro-toolkit.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url('https://www.paidmembershipspro.com/add-ons/pmpro-toolkit/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro' ) . '</a>',
			'<a href="' . esc_url('https://www.paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro' ) . '</a>',
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmprodev_plugin_row_meta', 10, 2 );