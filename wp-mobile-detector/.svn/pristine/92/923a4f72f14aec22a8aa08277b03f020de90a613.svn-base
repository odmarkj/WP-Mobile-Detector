<?php
/**
 Plugin Name: WP Mobile Detector
 Plugin URI: http://www.websitez.com/
 Description: Create a mobile friendly WordPress website instantly for over 5,000+ mobile devices.
 Version: 3.7
 Author: Websitez.com, LLC
 Author URI: http://www.websitez.com
 Text Domain: wp-mobile-detector
 Domain Path: /locale
*/

define('WEBSITEZ_MODE', 'production');

/*
Get the necessary files
*/
require(dirname(__FILE__) . '/functions.php');

global $table_prefix;

global $websitez_mobile_device;
$websitez_mobile_device = array();

if(isset($_GET['websitez-mobile'])):
	//Disabling W3 Total Cache
	define('DONOTCACHEPAGE', true);
	define('DONOTMINIFY', true);
	define('DONOTCACHEOBJECT', true);
endif;
/*
Define Globals
*/
define('WEBSITEZ_PLUGIN_NAME', 'WP Mobile Detector');
define('WEBSITEZ_PLUGIN_VERSION', '3.3');
define('WEBSITEZ_PLUGIN_AUTHORIZATION', 'wp_mobile_detector_token');
define('WEBSITEZ_PLUGIN_DIR', dirname(__FILE__));
define('WEBSITEZ_PLUGIN_WEB_DIR', plugin_dir_url(__FILE__));
define('WEBSITEZ_BASIC_THEME', 'websitez_basic_theme');
define('WEBSITEZ_ADVANCED_THEME', 'websitez_advanced_theme');
define('WEBSITEZ_INSTALL_BASIC_THEME', 'amanda-mobile');
define('WEBSITEZ_INSTALL_ADVANCED_THEME', 'amanda-mobile');
define('WEBSITEZ_DEFAULT_THEME', 'twentyten');
define('WEBSITEZ_ADVANCED_MAX_IMAGE_WIDTH', '320');
define('WEBSITEZ_STATS_TABLE', $table_prefix.'websitez_stats');
define('WEBSITEZ_RECORD_STATS_NAME', 'websitez_record_stats');
define('WEBSITEZ_RECORD_STATS', "true");
define('WEBSITEZ_SHOW_ATTRIBUTION_NAME', 'websitez_show_attribution');
define('WEBSITEZ_SHOW_ATTRIBUTION', "false");
define('WEBSITEZ_USE_PREINSTALLED_THEMES', "true");
define('WEBSITEZ_USE_PREINSTALLED_THEMES_NAME', "websitez_preinstalled_themes");
define('WEBSITEZ_BASIC_URL_REDIRECT', 'websitez_basic_url_redirect');
define('WEBSITEZ_ADVANCED_URL_REDIRECT', 'websitez_advanced_url_redirect');
define('WEBSITEZ_DISABLE_PLUGIN_NAME', 'websitez_disable_plugin');
define('WEBSITEZ_DISABLE_PLUGIN', "false");
define('WEBSITEZ_REDIRECT_MOBILE_VISITORS_NAME', 'websitez_redirect_mobile_visitors');
define('WEBSITEZ_REDIRECT_MOBILE_VISITORS', "false");
define('WEBSITEZ_REDIRECT_MOBILE_VISITORS_WEBSITE_NAME', 'websitez_redirect_mobile_visitors_website');
define('WEBSITEZ_REDIRECT_MOBILE_VISITORS_WEBSITE', "");
define('WEBSITEZ_LICENSE_KEY_NAME','websitez_pro_license_key');
define('WEBSITEZ_LICENSE_EMAIL_NAME','websitez_pro_license_email');
define('PURCHASE_WEBSITEZ_PRO_LINK','http://websitez.com');

//Does this plugin come with pre-installed templates?
global $websitez_preinstalled_templates;
$websitez_preinstalled_templates = get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME);

//Configuration options
global $websitez_options, $websitez_preload_images;

// Install plugin
if(function_exists('register_activation_hook')) {
	register_activation_hook( __FILE__, 'websitez_install' );
}
if(function_exists('register_deactivation_hook')) {
	register_deactivation_hook( __FILE__, 'websitez_uninstall' );
}

if(is_admin()) {
	require(dirname(__FILE__) . '/admin/admin-page.php');
	add_action('admin_menu', 'websitez_configuration_menu');
	//Check to make sure plugin is installed properly
	add_action('admin_init', 'websitez_checkInstalled');
	add_filter( 'cron_schedules', 'wz_cron_add_monthly' );
	add_filter('plugin_action_links', 'websitez_settings_link', 10, 2 );
	add_action("activated_plugin", "websitez_plugin_activated");
	add_action('wp_ajax_websitez_options', 'websitez_save_options');
}else{
	// Using an action does not allow us to disable other plugins.
	//add_action( 'plugins_loaded', 'websitez_go', 1 );
	websitez_go();
}

function websitez_go(){
	global $websitez_preinstalled_templates;
	
	$websitez_detect = apply_filters( 'websitez_detect', true );
	if ( $websitez_detect !== true ) {
		return false;
	}
	
	if(websitez_check_and_act_mobile()){
		if($websitez_preinstalled_templates == "true"){
			require(dirname(__FILE__) . '/default-widgets.php');
			add_filter('theme_root', 'websitez_setThemeFolder');
			add_filter('theme_root_uri', 'websitez_setThemeFolderFront');
			add_filter('stylesheet', 'websitez_getTheme');
			add_filter('template', 'websitez_getTheme');
			//If the user creates a dynamic sidebar, make sure to add the proper styling
			add_filter('dynamic_sidebar_params', 'websitez_reclamation_sidebar_params');
			add_action('widgets_init', 'websitez_unregister_default_wp_widgets', 1);
			add_action('send_headers', 'websitez_send_headers');
			add_filter('get_the_generator_xhtml', 'websitez_wordpress_generator');
			add_filter('get_the_generator_html', 'websitez_wordpress_generator');
		}else{
			add_filter('stylesheet', 'websitez_getTheme');
			add_filter('template', 'websitez_getTheme');
			add_action('send_headers', 'websitez_send_headers');
			add_filter('get_the_generator_xhtml', 'websitez_wordpress_generator');
			add_filter('get_the_generator_html', 'websitez_wordpress_generator');
		}
	}
}
add_action( 'websitez_do_filter', 'websitez_do_filter' );
?>