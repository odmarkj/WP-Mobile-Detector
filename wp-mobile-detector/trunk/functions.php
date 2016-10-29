<?php
add_action('init', 'websitez_options_api_request');

function websitez_options_api_request(){
	if(stripos($_SERVER['REQUEST_URI'],"/api/websitez-options.json") !== false){
		header("Content-Type: application/json");
		echo json_encode(websitez_get_options());
		exit();
	}
}

function websitez_check_for_update(){
	$websitez_options = websitez_get_options();
	$data = array(
		'wc-api' => 'upgrade-api',
		'request' => 'pluginupdatecheck',
		'plugin_name' => WEBSITEZ_PLUGIN_NAME,
		'version' => 'wp-mobile-detector/websitez-wp-mobile-detector.php',
		'software_version' => '3.1',
		'product_id' => 'WP Mobile Detector',
		'activation_email' => get_option(WEBSITEZ_LICENSE_EMAIL_NAME),
		'api_key' => get_option(WEBSITEZ_LICENSE_KEY_NAME),
		'domain' => 'websitez.com',
		'instance' => $websitez_options['general']['password']
	);
	$args = array(
		'timeout' => 15,
		'user-agent' => 'WordPress-Admin-Upgrade-Page'
	);
	$url = 'https://websitez.com/?'.http_build_query($data);
	$response = wp_remote_get( $url, $args );
	var_dump($response);
}

function websitez_gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function wz_cron_add_monthly( $schedules ) {
	// Adds once weekly to the existing schedules.
	$schedules['monthly'] = array(
		'interval' => (604800*4),
		'display' => __( 'Once Monthly' )
	);
	return $schedules;
}

function websitez_preload_image($src){
	global $websitez_preload_images;
	if(!in_array($src, $websitez_preload_images)){
		$websitez_preload_images[] = $src;
	}
}

function websitez_post_results($query){
	$websitez_options = websitez_get_options();
	if(is_numeric($websitez_options['general']['posts_per_page']) && $websitez_options['general']['posts_per_page'] > 0){
		$query->query_vars['posts_per_page'] = $websitez_options['general']['posts_per_page'];
	}
	return $query;
}

function websitez_include_functions_file( $file_name, $template_path, $current_path, $load_type ) {
	// Figure out real name of the source file
	$source_file = $file_name;

	if ( !file_exists( $source_file ) ) {
		$source_file = $current_path . '/' . $file_name;
		if ( !file_exists( $source_file ) ) {
			$source_file = $template_path . '/' . $file_name;
			if ( !file_exists( $source_file ) ) {
				echo 'Unable to load desktop functions file';
				die;
			}
		}
	}

	// Determine name of cached file
	$file_info = pathinfo( $source_file );
	//$cached_file = $file_info['dirname'] . '/.' . $file_info['basename'] . '.websitez';
	$cached_file = WEBSITEZ_PLUGIN_DIR."/cache/".$file_info['basename'].".websitez";

	// Basic caching for generating new functions files
	$generate_new_cached_file = true;
	if ( file_exists( $cached_file ) ) {
		$cached_file_mod_time = filemtime( $cached_file );
		$time_since_last_update = time() - $cached_file_mod_time;

		// Only update once an hour
		if ( $time_since_last_update < 10800 ) {
			$generate_new_cached_file = false;
		}
	}

	// Only generate cached file when it's stale or unavailable
	if ( $generate_new_cached_file ) {
		$contents = websitez_generate_functions_file( $file_name, $template_path, $current_path );

		$f = fopen( $cached_file, 'wt+' );
		if ( $f ) {
			fwrite( $f, $contents );
			fclose( $f );
		}
	}

	// Load cached file
	switch( $load_type ) {
		case 'include':
			include( $cached_file );
			break;
		case 'include_once';
			include_once( $cached_file );
			break;
		case 'require';
			require( $cached_file );
			break;
		case 'require_once';
			require_once( $cached_file );
			break;
		default:
			break;
	}
}

function websitez_generate_functions_file( $file_name, $template_path, $current_path ) {
	$path_info = pathinfo( $file_name );

	$original_name = $file_name;
	$file_name = $path_info['basename'];

	if ( !file_exists( $original_name ) ) {
		$test_name = $current_path . '/' . $file_name;
		if ( !file_exists( $test_name ) ) {
			$test_name = ABSPATH . '/' . $file_name;
			if ( !file_exists( $test_name ) ) {
				$test_name = $current_path . '/' . $original_name;
				if ( !file_exists( $test_name ) ) {
					die( 'Unable to properly load functions.php from the desktop theme, problem with ' . $test_name );
				} else {
					$file_name = $test_name;
				}
			} else {
				$file_name = $test_name;
			}
		} else {
			$file_name = $test_name;
		}
	} else {
		$file_name = $original_name;
	}

	if ( strpos( $file_name, $template_path ) === FALSE ) {
		return;
	}

	$file_contents = trim( file_get_contents( $file_name ) );

	$already_included_list = array();

	// Replace certain files
	$replace_constants = array( 'TEMPLATEPATH', 'STYLESHEETPATH', 'get_template_directory()' );
	foreach( $replace_constants as $to_replace ) {
		$file_contents = str_replace( $to_replace, "'" . $template_path . "'", $file_contents );
	}

	$file_contents = str_replace( ' bloginfo(', ' websitez_desktop_bloginfo(', $file_contents );
	$file_contents = str_replace( ' get_bloginfo(', ' websitez_get_desktop_bloginfo(', $file_contents );

	$include_params = array( 'include', 'include_once', 'require', 'require_once', 'locate_template' );
	foreach( $include_params as $include_param ) {
		$reg_ex = '#' . $include_param . ' *\((.*)\);#';
		if ( preg_match_all( $reg_ex, $file_contents, $match ) ) {
			for( $i = 0; $i < count( $match[0] ); $i++ ) {
				$statement_in_code_that_loads_file = $match[0][$i];

				$new_statement = str_replace( $include_param . ' (', $include_param . '(', $statement_in_code_that_loads_file );

				if ( $include_param == 'locate_template' ) {
					$new_statement = str_replace( $include_param . '(', 'websitez_locate_template(', $new_statement );

					$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "');", $new_statement );

					$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
				} else {

					$current_path = dirname( $file_name );
					$new_statement = str_replace( $include_param . '(', 'websitez_include_functions_file(', $new_statement );

					$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "', '" . $include_param . "');", $new_statement );

					$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
				}
			}
		}
	}

	return $file_contents;
}

function websitez_is_paid(){
	$license_email = get_option(WEBSITEZ_LICENSE_EMAIL_NAME);
	$license_key = get_option(WEBSITEZ_LICENSE_KEY_NAME);
	if(strlen($license_key) > 0 && strlen($license_email) > 0){
		return true;
	}
	
	return false;
}

function websitez_locate_template( $param1, $param2, $param3, $param4 = false, $param5 = false ) {
	$template_path = false;
	$current_path = false;
	$require_once = true;

	if ( $param4 ) {
		if ( $param5 ) {
			// 5 parameters
			$template_path = $param4;
			$current_path = $param5;
			$require_once = $param3;
		} else {
			// 4 parameters
			$template_path = $param3;
			$current_path = $param4;
		}
	} else {
		// 3 parameters
		$template_path = $param2;
		$current_path = $param3;
	}

	$template_file = $template_path . '/' . $param1;
	if ( !file_exists( $template_file ) ) {
		$template_file = $current_path . '/' . $param1;
	}

	if ( file_exists( $template_path ) ) {

		$current_path = dirname( $template_file );
		if ( $require_once ) {
			websitez_include_functions_file( $template_file, $template_path, $current_path, 'require_once' );
		} else {
			websitez_include_functions_file( $template_file, $template_path, $current_path, 'require' );
		}
	} else {
		// add debug statement
	}
}

function websitez_get_desktop_bloginfo( $param ) {
	switch( $param ) {
		case 'stylesheet_directory':
		case 'template_url':
		case 'template_directory':
			return content_url() . '/themes/' . get_option( 'template' );
		default:
			return get_bloginfo( $param );
	}
}

function websitez_desktop_bloginfo( $param ) {
	echo websitez_get_desktop_bloginfo( $param );
}

function websitez_load_functions_file_for_desktop(){
	$websitez_options = websitez_get_options();
	$desktop_theme_directory = get_theme_root() . '/'. get_template();
	$desktop_functions_file = $desktop_theme_directory . '/functions.php';

	// Check to see if the theme has a functions.php file
	if ( file_exists( $desktop_functions_file ) ) {
		switch( $websitez_options['general']['load_desktop_functions_method'] ) {
			case 'translate':
				websitez_include_functions_file( $desktop_functions_file, dirname( $desktop_functions_file ), dirname( $desktop_functions_file ), 'require_once' );
				break;
			default:
				require_once( $desktop_functions_file );
				break;
		}
	}
}

function websitez_plugin_activated(){
	$websitez_options = websitez_get_options();
	if(strlen($websitez_options['general']['disable_plugins']) > 0){
		$websitez_options['general']['update_disable_plugins'] = true;
		$s = websitez_set_options($websitez_options);
	}
}

function websitez_settings_link( $links, $file ) {
 	if( $file == 'wp-mobile-detector/websitez-wp-mobile-detector.php' && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=websitez_themes' ) . '">' . __('Settings') . '</a>';
		array_push( $links, $settings_link ); // after other links
	}
	return $links;
}

function websitez_disable_other_plugin(){
	return true;
}

function websitez_catch_homepage($type){
	$websitez_options = websitez_get_options();
	if(strlen($websitez_options['general']['mobile_home_page']) > 0){
		return 'page';
	}
	return $type;
}

function websitez_set_homepage($page_id){
	$websitez_options = websitez_get_options();
	if(strlen($websitez_options['general']['mobile_home_page']) > 0){
		return (int)$websitez_options['general']['mobile_home_page'];
	}
	return $page_id;
}

/*
$contents = websitez_generate_functions_file( $file_name, $template_path, $current_path );
*/

function websitez_generate_functions_file_old( $file_name, $template_path, $current_path ) {
	$fin_file_name = WEBSITEZ_PLUGIN_DIR."/cache/desktop-functions.php";
	
	if(file_exists($fin_file_name)){
		$cached_file_mod_time = filemtime( $fin_file_name );
		$time_since_last_update = time() - $cached_file_mod_time;
		if ( $time_since_last_update < 10800 ) {
			return $fin_file_name;
		}
	}
	
	$path_info = pathinfo( $file_name );

	$original_name = $file_name;
	$file_name = $path_info['basename'];

	if ( !file_exists( $original_name ) ) {
		$test_name = $current_path . '/' . $file_name;
		if ( !file_exists( $test_name ) ) {
			$test_name = ABSPATH . '/' . $file_name;
			if ( !file_exists( $test_name ) ) {
				$test_name = $current_path . '/' . $original_name;
				if ( !file_exists( $test_name ) ) {
					die( 'Unable to properly load functions.php from the desktop theme, problem with ' . $test_name );
				} else {
					$file_name = $test_name;
				}
			} else {
				$file_name = $test_name;
			}
		} else {
			$file_name = $test_name;
		}
	} else {
		$file_name = $original_name;
	}

	if ( strpos( $file_name, $template_path ) === FALSE ) {
		return;
	}

	$file_contents = "";
	$f = fopen( $file_name, 'rb' );
	if ( $f ) {
		while ( !feof( $f ) ) {
			$file_contents .= fread( $f, 8192 );
		}

		fclose( $f );
	}

	$already_included_list = array();

	// Replace certain files
	$replace_constants = array( 'TEMPLATEPATH', 'STYLESHEETPATH', 'get_template_directory()' );
	foreach( $replace_constants as $to_replace ) {
		$file_contents = str_replace( $to_replace, "'" . $template_path . "'", $file_contents );
	}

	$file_contents = str_replace( ' bloginfo(', ' websitez_desktop_bloginfo(', $file_contents );
	$file_contents = str_replace( ' get_bloginfo(', ' websitez_get_desktop_bloginfo(', $file_contents );

	$include_params = array( 'include', 'include_once', 'require', 'require_once', 'locate_template' );
	foreach( $include_params as $include_param ) {
		$reg_ex = '#' . $include_param . ' *\((.*)\);#';
		if ( preg_match_all( $reg_ex, $file_contents, $match ) ) {
			for( $i = 0; $i < count( $match[0] ); $i++ ) {
				$statement_in_code_that_loads_file = $match[0][$i];

				$new_statement = str_replace( $include_param . ' (', $include_param . '(', $statement_in_code_that_loads_file );

				if ( $include_param == 'locate_template' ) {
					$new_statement = str_replace( $include_param . '(', 'websitez_locate_template(', $new_statement );

					$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "');", $new_statement );

					$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
				} else {

					$current_path = dirname( $file_name );
					$new_statement = str_replace( $include_param . '(', 'websitez_include_functions_file(', $new_statement );

					$new_statement = str_replace( ');', ", '" . $template_path . "', '" . $current_path . "', '" . $include_param . "');", $new_statement );

					$file_contents = str_replace( $statement_in_code_that_loads_file, $new_statement, $file_contents );
				}
			}
		}
	}

	$f = fopen( $fin_file_name, 'wt+' );
	if ( $f ) {
		$w = fwrite( $f, $file_contents );
		fclose( $f );
		if($w !== false){
			return $fin_file_name;
		}
	}
	
	return false;
}

function websitez_chmod_cache(){
	global $wp_filesystem;
	$plugin_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WEBSITEZ_PLUGIN_DIR);
	$cache_path = $plugin_path."/cache/";
	$chmod = $wp_filesystem->chmod($cache_path, 0777);
	if($chmod){
		return true;
	}
	
	return false;
}

function websitez_generate_backup(){
	global $wp_filesystem;
	$backup_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WEBSITEZ_PLUGIN_DIR."/cache/backup.txt");
	$data = array();
	$data['websitez-options'] = websitez_get_options();
	$data['advanced-theme'] = get_option(WEBSITEZ_ADVANCED_THEME);
	$data['basic-theme'] = get_option(WEBSITEZ_BASIC_THEME);
	$saved = $wp_filesystem->put_contents($backup_path, json_encode($data));
	if($saved){
		return true;
	}
	
	return false;
}

function websitez_get_options(){
	if(!isset($GLOBALS) || !is_array($GLOBALS['websitez_options'])):
		$websitez_options = get_option('websitez-options');
		if(!is_array($websitez_options)):
			$websitez_options = unserialize($websitez_options);
		endif;
		$GLOBALS['websitez_options'] = $websitez_options;
	else:
		$websitez_options = $GLOBALS['websitez_options'];
	endif;
	
	return $websitez_options;
}

function websitez_set_options($data){
	try{
		unset($GLOBALS['websitez_options']);
		return update_option("websitez-options", serialize($data));
	} catch (Exception $e) {
		// $e->getMessage()
	}
	
	return false;
}

function websitez_mobile_title($content, $attr){
	if($attr == "name"):
		$websitez_options = websitez_get_options();
		if(strlen($websitez_options['general']['mobile_title']) > 0):
			return $websitez_options['general']['mobile_title'];
		endif;
	endif;
	
	return $content;
}

function websitez_get_mobile_device(){
	global $websitez_mobile_device;
	return $websitez_mobile_device;
}
function websitez_set_mobile_device($mobile_device){
	global $websitez_mobile_device;
	$websitez_mobile_device = $mobile_device;
}
/*
Insert proper meta tags for caching and attribution
*/
function websitez_wordpress_generator($generator) {
	$headers = "\n";
	$headers .= '<meta http-equiv="Cache-Control" content="max-age=200" />';
	$headers .= "\n";
	$headers .= '<meta name="generator" content="WordPress ' . get_bloginfo( 'version' ) . ' - Mobile Detection by '.WEBSITEZ_PLUGIN_NAME.'" />';
	$headers .= "\n";
  return $headers;
}
/*
Send header to let them requester know that it was mobilized
*/
function websitez_send_headers($wp) {
  @header("X-Mobilized-By: ".WEBSITEZ_PLUGIN_NAME);
}

/*
This will style a dynamic sidebar if one is created by the website owner
*/
function websitez_reclamation_sidebar_params($params){
	$params[0]['before_widget'] = '<div class="wrapper"><div class="ui-body ui-body-f"><div data-role="collapsible" data-theme="a">';
	$params[0]['before_title'] = '<h3>';
	if($params[0]['widget_name'] == "Calendar" || $params[0]['widget_name'] == "Text" || $params[0]['widget_name'] == "Tag Cloud"){
		$params[0]['after_title'] = '</h3><p>';
		$params[0]['after_widget'] = '</p></div></div></div>';
	}else{
		$params[0]['after_title'] = '</h3><ul data-role="listview" data-inset="true" data-theme="c">';
		$params[0]['after_widget'] = '</ul></div></div></div>';
	}
	
	return $params;
} 

function websitez_default_settings(){
	return array(
		'colors' => array(
			"custom_color_light"=>"4f7498",
			"custom_color_medium_light"=>"abbdce",
			"custom_color_dark"=>"3c5975",
			"default_link_color"=>"3c5975",
			"custom_post_background"=>"ffffff",
			"custom_header_logo"=>"f5f5f5"
		),
		'general' => array(
			"mobile_home_page"=>"",
			"selected_mobile_theme"=>"amanda-mobile",
			"posts_per_page"=>"10",
			"display_to_tablet"=>"no",
			"record_stats" => "true"
		),
		'analytics' => array(
			"show_analytics"=>"no",
			"show_analytics_snippet"=>""
		),
		'ads' => array(
			"show_header"=>"no",
			"show_header_snippet"=>"",
			"show_footer"=>"no",
			"show_footer_snippet"=>""
		),
		'images' => array(
			"header_left_icon"=>"images/ico/1_shirts.png",
			"custom_website_background"=>"images/bg.gif",
			"logo"=>""
		),
		'sidebar' => array(
			"menu_order"=>"show_search,show_menu,show_pages,show_categories,show_meta",
			"show_menu"=>"yes",
			"show_pages"=>"yes",
			"show_pages_items"=>"",
			"show_categories"=>"yes",
			"show_categories_items"=>"",
			"show_meta"=>"yes",
			"show_search"=>"yes"
		)
	);
}

/*
This is called on activation of the plugin
*/
function websitez_install(){
	global $wpdb, $websitez_preinstalled_templates, $table_prefix;

	/*
	Setup the stats table to record mobile visits
	*/
	$table_name = WEBSITEZ_STATS_TABLE;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			id int(11) NOT NULL AUTO_INCREMENT,
			data text NOT NULL,
			device_type int(2) NOT NULL,
			created_at DATETIME NOT NULL,
			UNIQUE KEY id (id),
			PRIMARY KEY(created_at)
			);";
	
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
	}
	
	//Default options for the customizable mobile theme
	$websitez_options_default = websitez_default_settings();
	
	//Set the default options if they do not exist
	$websitez_options = websitez_get_options();
	if(!$websitez_options){
		websitez_set_options($websitez_options_default);
	}
	
	if(!get_option(WEBSITEZ_LICENSE_KEY_NAME))
		add_option(WEBSITEZ_LICENSE_KEY_NAME, WEBSITEZ_LICENSE_KEY, '', 'yes');
	
	if(!get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME))
		add_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME, WEBSITEZ_USE_PREINSTALLED_THEMES, '', 'yes');

	if(!get_option(WEBSITEZ_ADVANCED_THEME)){
		if(WEBSITEZ_USE_PREINSTALLED_THEMES == "true")
			add_option(WEBSITEZ_ADVANCED_THEME, WEBSITEZ_INSTALL_ADVANCED_THEME, '', 'yes');
		else
			add_option(WEBSITEZ_ADVANCED_THEME, WEBSITEZ_DEFAULT_THEME, '', 'yes');
	}
	
	if(!get_option(WEBSITEZ_BASIC_THEME)){
		if(WEBSITEZ_USE_PREINSTALLED_THEMES == "true")
			add_option(WEBSITEZ_BASIC_THEME, WEBSITEZ_INSTALL_BASIC_THEME, '', 'yes');
		else
			add_option(WEBSITEZ_BASIC_THEME, WEBSITEZ_DEFAULT_THEME, '', 'yes');
	}
}

/*
Remove all traces of the plugin
This is not currently in use, but may be implemented TODO
*/
function websitez_uninstall(){
	global $wpdb;
	if(get_option(WEBSITEZ_BASIC_THEME))
		delete_option(WEBSITEZ_BASIC_THEME);
	if(get_option(WEBSITEZ_ADVANCED_THEME))
		delete_option(WEBSITEZ_ADVANCED_THEME);
	
	$table_name = WEBSITEZ_STATS_TABLE;//TODO
	if($wpdb->get_var("show tables like '$table_name'") == $table_name) {
		$sql = "DROP TABLE ".$table_name;
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);
	}
}

/*
Check to make sure authorization token is set.
*/
function websitez_authorization(){
	$token = get_option(WEBSITEZ_PLUGIN_AUTHORIZATION);
	if(!$token){
		$response = unserialize(websitez_remote_request("http://stats.websitez.com/get_token.php","host=".$_SERVER['HTTP_HOST']."&email=".get_option('admin_email')."&source=wp-mobile-detector"));
		if($response && $response['status'] == "1" && strlen($response['token']) > 0){
			update_option(WEBSITEZ_PLUGIN_AUTHORIZATION,$response['token']);
		}
	}
}

/*
Start the buffer to filter the raw contents of a page
*/
function websitez_basic_buffer(){
	//Don't filter Dashboard pages and the feed
	if (is_feed() || is_admin()){
		return;
	}

	ob_start("websitez_filter_basic_page");
}

/*
Start the buffer to filter the raw contents of a page
*/
function websitez_advanced_buffer(){
	//Don't filter Dashboard pages and the feed
	if (is_feed() || is_admin()){
		return;
	}
	
	ob_start("websitez_filter_advanced_page");
}

/*
Filter content for an advanced mobile device
*/
function websitez_filter_advanced_page($html){
	if (class_exists('DOMDocument')) {
		try{
			//Resize the images on the page
			$dom = new DOMDocument();
			$dom->loadHTML($html);
			
			// grab all the on the page and make sure they are the right size
			$xpath = new DOMXPath($dom);
			$imgs = $xpath->evaluate("/html/body//img");
			
			for ($i = 0; $i < $imgs->length; $i++) {
				$img = $imgs->item($i);
				$src = trim($img->getAttribute('src'));
				$img->removeAttribute('width');
				$img->removeAttribute('height');
				//Use dynamic image resizer link
				if(strlen($src) > 0){
					$max_width = WEBSITEZ_ADVANCED_MAX_IMAGE_WIDTH;
					list($width, $height) = getimagesize($src);
					$blog_url = get_bloginfo('siteurl');
					if($width > $max_width){
						if(stripos($src,$blog_url) !== false):
							$arr = explode("/",$src);
							if(count($arr) > 4):
								unset($arr[0]);
								unset($arr[1]);
								unset($arr[2]);
								$src = "/".implode("/",$arr);
							endif;
						endif;
						$tmp = parse_url($src);
						if(strlen($tmp['host']) > 0):
							$path = $tmp['scheme']."://".$tmp['host'].$tmp['path'];
						else:
							$path = $tmp['path'];
						endif;
						$resize = plugin_dir_url(__FILE__)."timthumb.php?src=".urlencode($path)."&w=".$max_width;
						$img->setAttribute('src', $resize);
					}
				}
			}
			
			if(isset($_GET['websitez-mobile'])){
				$styles = $xpath->evaluate("/html//link");
				for ($i = 0; $i < $styles->length; $i++) {
					$style = $styles->item($i);
					$href = trim($style->getAttribute('href'));
					if(stripos($href, "?") !== false){
						$new_link = $href."&wzv=".rand(500, 500000);
					}else{
						$new_link = $href."?wzv=".rand(500, 500000);
					}
					
					$style->setAttribute('href', $new_link);
				}
			}
			
			$stuff = $dom->saveHTML();
		}catch(Exception $e){
			$stuff = $html;
		}
	}else{
		$stuff = $html;
	}
	
	return $stuff;
}

/*
Filter content for a basic mobile device
*/
function websitez_filter_basic_page($html){
	global $websitez_preinstalled_templates;

	if (class_exists('DOMDocument')) {
		try{
			//Resize the images on the page
			$dom = new DOMDocument();
			$dom->loadHTML($html);
			
			// grab all the on the page and make sure they are the right size
			$xpath = new DOMXPath($dom);
			$divs = $xpath->evaluate("/html/body//div");
			
			for ($i = 0; $i < $divs->length; $i++) {
				$div = $divs->item($i);
				$div->removeAttribute('data-role');
				$div->removeAttribute('data-theme');
				$div->removeAttribute('style');
				$div->removeAttribute('data-icon');
				$div->removeAttribute('data-iconpos');
				$div->removeAttribute('onclick');
				$div->removeAttribute('data-state');
			}
			
			$links = $xpath->evaluate("/html/body//a");
			
			for ($i = 0; $i < $links->length; $i++) {
				$link = $links->item($i);
				$link->removeAttribute('data-inline');
				$link->removeAttribute('data-role');
				$link->removeAttribute('data-theme');
				$link->removeAttribute('style');
				$link->removeAttribute('data-icon');
				$link->removeAttribute('data-iconpos');
				$link->removeAttribute('onclick');
			}
			
			$uls = $xpath->evaluate("/html/body//ul");
			
			for ($i = 0; $i < $uls->length; $i++) {
				$ul = $uls->item($i);
				$ul->removeAttribute('data-inline');
				$ul->removeAttribute('data-role');
				$ul->removeAttribute('data-theme');
				$ul->removeAttribute('data-inset');
				$ul->removeAttribute('style');
				$ul->removeAttribute('data-icon');
				$ul->removeAttribute('data-iconpos');
				$ul->removeAttribute('onclick');
			}
			
			$htmls = $xpath->evaluate("/html");
			
			for ($i = 0; $i < $htmls->length; $i++) {
				$h = $htmls->item($i);
				$h->removeAttribute('dir');
				$h->removeAttribute('lang');
			}
			
			$text = $dom->saveHTML();
		}catch(Exception $e){
			$text = $html;
		}
	}else{
		$text = $html;
	}
	
	$text = preg_replace(
	  array(
	  	// Remove invisible content
	  	'@<meta[^>]*?>@siu',
	  	'@<link[^>]*?>@siu',
	  	'@<form[^>]*?>.*?</form>@siu',
	    '@<style[^>]*?>.*?</style>@siu',
	    '@<script[^>]*?.*?</script>@siu',
	    '@<object[^>]*?.*?</object>@siu',
	    '@<embed[^>]*?.*?</embed>@siu',
	    '@<applet[^>]*?.*?</applet>@siu',
	    '@<noframes[^>]*?.*?</noframes>@siu',
			'@<iframe[^>]*?.*?</iframe>@siu',
	    '@<noscript[^>]*?.*?</noscript>@siu',
	    '@<noembed[^>]*?.*?</noembed>@siu',
			// Remove visible content
			'@<img[^>]*?>@siu',
	  	// Add line breaks before and after blocks
	    '@</?((address)|(blockquote)|(center)|(del))@iu',
	    '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
	    '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
	    '@</?((table)|(th)|(td)|(caption))@iu',
	    '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
	    '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
	    '@</?((frameset)|(frame)|(iframe))@iu',
	  ),
	  array(
	    ' ',' ',' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
	    "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
	    "\n\$0", "\n\$0",
	  ),
	  $text );
  //If it is a websitez template, run the basic device stylesheet
  if($websitez_preinstalled_templates == "true")
  	$text = str_replace("</head>","<link rel='stylesheet' href='".get_bloginfo('stylesheet_directory')."/basic-device.css' />\n</head>\n",$text);
	
	$text = preg_replace('/\s\s+/', '', $text);
  $text = preg_replace('/<!--(.*?)-->/', '', $text);
  $text = preg_replace('/\n/', '', $text);
		
	return $text;
}

/*
When in the admin area, this will alert the admin if the plugin is not installed properly
*/
function websitez_checkInstalled(){
	global $wpdb,$table_prefix;
	$websitez_options = websitez_get_options();
	if($websitez_options['general']['update_disable_plugins'] == true){
		$websitez_options['general']['update_disable_plugins'] = false;
		$s = websitez_set_options($websitez_options);
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>A plugin was modified which requires updating the plugin <strong>".WEBSITEZ_PLUGIN_NAME."</strong>. Please <a href=\"admin.php?page=websitez_themes&disabled_plugins=true\">click here</a> to perform the update instantly.</p></div>';" ) );
	}
	if(isset($_GET['websitez-plugin-notice'])):
		update_option('WEBSITEZ_OTHER_PLUGINS_CHECK', 'false');
	endif;
	$table = $table_prefix."options";
	if(!get_option(WEBSITEZ_BASIC_THEME) || !get_option(WEBSITEZ_ADVANCED_THEME)){
		if(websitez_is_paid() != true){
			add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".WEBSITEZ_PLUGIN_NAME." was unable to install correctly. Please try deactivating and then activating this plugin again.</p><p><strong>If you still have trouble, please contact support@websitez.com</strong></p></div>';" ) );
		}else{
			add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".WEBSITEZ_PLUGIN_NAME." was unable to install correctly. This domain is not authorized to use this plugin.</p><p><strong>Please contact support@websitez.com</strong></p></div>';" ) );
		}
	}
	$plugin_notice = false;
	$plugins = get_option('active_plugins');
	foreach($plugins as $plugin):
		if(stripos($plugin,"w3-total-cache") !== false):
			$plugin_notice = true;
		endif;
	endforeach;
	if(get_option('WEBSITEZ_OTHER_PLUGINS_CHECK') == 'false'):
		$plugin_notice = false;
	endif;
	if($plugin_notice):
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>There are plugins installed that require slight modifications to work with the <strong>".WEBSITEZ_PLUGIN_NAME."</strong> plugin. Please read this short blog post that will help you resolve these issues quickly: <a href=\"http://websitez.com/resolving-plugin-conflicts-with-wp-mobile-detector/\" target=\"_blank\">http://websitez.com/resolving-plugin-conflicts-with-wp-mobile-detector/</a></p><p><a href=\"?websitez-plugin-notice=hide\">Hide This Notice</a></p></div>';" ) );
	endif;
	$cache = WEBSITEZ_PLUGIN_DIR.'/cache/';
	$permissions = substr(sprintf('%o', fileperms($cache)), -4);
	if($permissions != "0777"):
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>To finish installing the <strong>WP Mobile Detector</strong>, we need your help. Please <a href=\"admin.php?page=websitez_themes&ftp=true\">click here</a>.</p></div>';" ) );
	endif;
	websitez_authorization();
}

/*
Change where it looks for themes on the frond end
*/
function websitez_setThemeFolderFront(){
	return plugin_dir_url(__FILE__).'/themes';
}

/*
Change where it looks for themes
*/
function websitez_setThemeFolder(){
	return WEBSITEZ_PLUGIN_DIR.'/themes';
}

/*
The theme set here is used if it is a mobile device
*/
function websitez_setTheme($theme){
	$GLOBALS['websitez_template_name'] = $theme;
}

/*
The theme retrieved here is used if it is a mobile device
*/
function websitez_getTheme(){
	return $GLOBALS['websitez_template_name'];
}

/*
Lets get this party started
*/
function websitez_check_and_act_mobile(){
	global $table_prefix, $wpdb, $websitez_preinstalled_templates;
	$mobile_device = websitez_detect_mobile_device();
	//Set the detection
	websitez_set_mobile_device($mobile_device);
	$websitez_options = websitez_get_options();
	
	//Is it a mobile device?
	if($mobile_device['status'] == true || $mobile_device['status'] == "1"){
		if($websitez_options['general']['load_desktop_functions'] == "yes"){
			//add_action( 'websitez_functions_start', 'websitez_load_functions_file_for_desktop');
			//websitez_load_functions_file_for_desktop();
			websitez_load_functions_file_for_desktop();
		}
		//Record a mobile visit only on the regular site and if it is enabled
		$websitez_record_stats = $websitez_options['general']['record_stats'];
		$websitez_preinstalled_templates = get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME);
		$websitez_disable_plugin = $websitez_options['general']['disable_plugin'];
		if($websitez_record_stats == "true" && !is_feed() && !is_admin()){
			$insert = $wpdb->insert(WEBSITEZ_STATS_TABLE, array( 'data' => serialize($_SERVER), 'device_type' => $mobile_device['type'], 'created_at' => date("Y-m-d H:i:s") ) );
		}
		
		/*
		This will disable redirecting a mobile user or showing a mobile theme, but still provide detection.
		*/
		if($websitez_disable_plugin=="true"):
			return;
		endif;
		
		if(strlen($websitez_options['general']['redirect_mobile_visitors_website']) > 5):
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".urldecode($websitez_options['general']['redirect_mobile_visitors_website']));
			exit();
		endif;

		if($mobile_device['type'] == "2"){ //Standard device
			add_filter('bloginfo', 'websitez_mobile_title', 10, 2);
			add_filter('pre_get_posts', 'websitez_post_results');
			if(strlen($websitez_options['general']['remove_shortcodes']) > 0){
				websitez_nullify_shortcodes($websitez_options['general']['remove_shortcodes']);
			}
			if(strlen($websitez_options['general']['mobile_home_page']) > 0){
				//add_filter('pre_option_show_on_front', 'websitez_catch_homepage');
				//add_filter('pre_option_page_on_front','websitez_set_homepage');
			}
			$option = get_option(WEBSITEZ_BASIC_THEME);
			if(($websitez_preinstalled_templates == "false" && is_dir(WP_CONTENT_DIR.'/themes/'.$option)) || ($websitez_preinstalled_templates == "true" && is_dir(WEBSITEZ_PLUGIN_DIR.'/themes/'.$option)) && strlen($option) > 0) {
				//This logic switches the theme and modifies the head/footer to give the user the ability to switch back to the full site
				websitez_setTheme($option);
				if ( function_exists( 'show_admin_bar' ) ) {
					add_filter( 'show_admin_bar', '__return_false' );
				}
				//This will remove all scripts, stylesheets, and advanced HTML from the page
				add_action('wp', 'websitez_basic_buffer', 10, 0);
				add_filter("the_content", "websitez_filterContentStandard");
				if(strlen($websitez_options['general']['remove_shortcodes']) > 0){
					add_filter("the_content", "websitez_removeShortcodes");
				}
				add_action('wp_footer', 'websitez_web_footer');
				add_action('wp_head', 'websitez_web_head');
				add_filter('wp', 'websitez_redirect');
				return true;
			}
		}else if($mobile_device['type'] == "1"){ //Smart device
			add_filter('bloginfo', 'websitez_mobile_title', 10, 2);
			add_filter('pre_get_posts', 'websitez_post_results');
			if(strlen($websitez_options['general']['disable_plugins']) > 0){
				$disabled = explode(",", $websitez_options['general']['disable_plugins']);
				foreach($disabled as $disable){
					if(strlen($disable) > 0){
						$parts = explode("/", $disable);
						add_filter($parts[0].'_disable', 'websitez_disable_other_plugin');
					}
				}
			}
			if(strlen($websitez_options['general']['remove_shortcodes']) > 0){
				websitez_nullify_shortcodes($websitez_options['general']['remove_shortcodes']);
			}
			if(strlen($websitez_options['general']['mobile_home_page']) > 0){
				//add_filter('option_show_on_front', 'websitez_catch_homepage');
				//add_filter('option_page_on_front','websitez_set_homepage');
			}
			$option = get_option(WEBSITEZ_ADVANCED_THEME);

			if(($websitez_preinstalled_templates == "false" && is_dir(WP_CONTENT_DIR.'/themes/'.$option)) || ($websitez_preinstalled_templates == "true" && is_dir(WEBSITEZ_PLUGIN_DIR.'/themes/'.$option)) && strlen($option) > 0) {
				//This logic switches the theme and modifies the head/footer to give the user the ability to switch back to the full site
				websitez_setTheme($option);
				if ( function_exists( 'show_admin_bar' ) ) {
					add_filter( 'show_admin_bar', '__return_false' );
				}
				add_action('wp', 'websitez_advanced_buffer', 10, 0);
				add_filter("the_content", "websitez_filterContentAdvanced");
				if(strlen($websitez_options['general']['remove_shortcodes']) > 0){
					add_filter("the_content", "websitez_removeShortcodes");
				}
				add_action('wp_footer', 'websitez_web_footer');
				add_action('wp_head', 'websitez_web_head');
				add_filter('wp', 'websitez_redirect');
				return true;
			}
		}else if($mobile_device['type'] == "0" && isset($_COOKIE['websitez_mobile_detector_fullsite'])){
			//If this is true, it is a mobile user, but they elected to view the full site.
			//We should give them the option to switch back
			add_action('wp_footer', 'websitez_web_footer_mobile');
			add_action('wp_head', 'websitez_web_head_mobile');
			//We want to return false so that the currently installed template is shown
			return false;
		}
	}else{
		//This means it is not a mobile device
	}
	return false;
}

function websitez_redirect(){
	$websitez_options = websitez_get_options();
	$page = (int)$websitez_options['general']['mobile_home_page'];
	if($page && is_front_page() && is_numeric($page) && websitez_get_page_id() != $page){
		//Make sure W3 Total Cache doesn't cache a blank page
		define('DONOTCACHEPAGE', true);
		define('DONOTMINIFY', true);
		define('DONOTCACHEOBJECT', true);
		header("HTTP/1.1 301 Moved Permanently");
		$query = $_SERVER['QUERY_STRING'];
		if(strlen($query) > 0){
			header('Location: '.get_permalink($page).'?'.$_SERVER['QUERY_STRING']);
		}else{
			header('Location: '.get_permalink($page));
		}
 		die;
	}
}

function websitez_filterContentStandard($content){
	//Remove all images
	$content = preg_replace("/<img[^>]+\>/i", "", $content);
	return $content;
}

function websitez_filterContentAdvanced($content){
	//For now, do not filter anything, possibly filter HTML5 tags such as canvas
	return $content;
}

function websitez_nullify_shortcode( $params ) {
	return '';
}

function websitez_nullify_shortcodes($shortcodes){
	if($shortcodes == "*"){
		global $shortcode_tags;
		if(count($shortcode_tags) > 0){
			$shortcodes = "";
			$i = 0;
			foreach($shortcode_tags as $code => $function){
				if($i != 0){
					$shortcodes .= ",";
				}
				$shortcodes .= $code;
				$i++;
			}
		}
	}
	$all_short_codes = explode( ',', str_replace( ', ', ',', $shortcodes ) );
	if ( $all_short_codes ) {
		foreach( $all_short_codes as $code ) {
			add_shortcode( $code, 'websitez_nullify_shortcode' );
		}
	}
}

function websitez_removeShortcodes($content){	
	$pattern = get_shortcode_regex();
	try{
		$content = preg_replace('/'. $pattern .'/s', '<!-- shortcode removed -->', $content);
	} catch (Exception $e) {
    	// $e->getMessage()
	}
	return $content;
}

function websitez_get_page_id(){
	$version = get_bloginfo('version');
	if ($version < 3.1) {
		global $wp_query;
		$page_object = $wp_query->get_queried_object();
		return $wp_query->get_queried_object_id();
	}else{
		$page_object = get_queried_object();
		return get_queried_object_id();
	}
	return false;
}

/*
Attribution and ability to switch between mobile and full site
*/
function websitez_web_footer(){
	global $websitez_preload_images;
	$websitez_options = websitez_get_options();
	$html = "<div class='websitez-footer'>";
	if($websitez_options['general']['show_attribution'] == "yes"){
		$html .= "<center><span style='font-size: .8em; padding: 3px;'>Powered by <a href='http://websitez.com'>WordPress Mobile Detector</a></small></center>";
	}
	$html .= "<center><a href='#' onClick='websitez_setFullSite(); return false;' rel='nofollow'>View Full Site</a></center>";
	$html .= "</div>";
	if(strlen($websitez_options['analytics']['show_analytics_snippet']) > 0){
		$html .= stripslashes($websitez_options['analytics']['show_analytics_snippet']);
	}
	if(strlen($websitez_options['theme']['sharing_icons']) > 0){
		$show = false;
		if((is_front_page() || is_home()) && $websitez_options['theme']['sharing_home'] == "yes"){
			$show = true;
		}elseif(is_single() && $websitez_options['theme']['sharing_posts'] == "yes"){
			$show = true;
		}elseif(is_page() && $websitez_options['theme']['sharing_pages'] == "yes"){
			$show = true;
		}elseif(is_category() && $websitez_options['theme']['sharing_categories'] == "yes"){
			$show = true;
		}elseif(is_archive() && $websitez_options['theme']['sharing_archives'] == "yes"){
			$show = true;
		}
		if($show == true && strlen($websitez_options['theme']['sharing_exclude']) > 0){
			$current_id = websitez_get_page_id();
			$ids = explode(",", $websitez_options['theme']['sharing_exclude']);
			foreach($ids as $k => $id){
				$ids[$k] = trim($id);
			}
			foreach($ids as $id){
				if($id == $current_id){
					$show = false;
					break;
				}
			}
		}
		if($show){
			wp_enqueue_script('jmobile', plugin_dir_url(__FILE__)."js/jMobile.min.js", array(), false, true);
			wp_enqueue_script('wz_share', plugin_dir_url(__FILE__)."js/sharing.php?path=".urlencode(WEBSITEZ_PLUGIN_WEB_DIR)."&icons=".urlencode($websitez_options['theme']['sharing_icons']), array(), false, true);
		}
	}
	if(count($websitez_preload_images) > 0){
		$html .= "<script type=\"text/javascript\">var sources = ".json_encode($websitez_preload_images)."; \n var images = []; \n for (i = 0, length = sources.length; i < length; ++i) { \nimages[i] = new Image(); \nimages[i].src = sources[i]; \n}\n</script>";
	}
	echo $html;
}

/*
Attribution and ability to switch between mobile and full site
*/
function websitez_web_footer_mobile(){
	$websitez_options = websitez_get_options();
	$html = "<div class='websitez-footer-mobile'>";
	if($websitez_options['general']['show_attribution'] == "yes"){
		$html .= "<center><span style='font-size: .8em; padding: 3px;'>Powered by <a href='http://websitez.com'>WordPress Mobile Detector</a></small></center>";
	}
	$html .= "<center><a href='#' onClick='websitez_setMobileSite(); return false;' rel='nofollow'>View Mobile Site</a></center>";
	$html .= "</div>";
	if(strlen($websitez_options['general']['show_analytics_snippet']) > 0){
		$html .= stripslashes($websitez_options['general']['show_analytics_snippet']);
	}
	echo $html;
}

/*
Attribution and ability to switch between mobile and full site
*/
function websitez_web_head(){
	$websitez_options = websitez_get_options();
	echo "<script type='text/javascript'>\n
	function websitez_setFullSite(){\n
		c_name = 'websitez_mobile_detector_fullsite';\n
		value = '1';\n
		expiredays = '1';\n
		var exdate=new Date();\n
		exdate.setDate(exdate.getDate()+expiredays);\n
		document.cookie=c_name+ '=' +escape(value)+((expiredays==null) ? '' : ';expires='+exdate.toUTCString());\n
		window.location.reload();\n
	}\n
	</script>\n";
	if(strlen($websitez_options['general']['ios_app_id']) > 0){
		echo '<meta name="apple-itunes-app" content="app-id='.$websitez_options['general']['ios_app_id'].'">';
	}
	if(strlen($websitez_options['general']['custom_styles']) > 0){
		echo '<style type="text/css">'.$websitez_options['general']['custom_styles'].'</style>';
	}
	if($websitez_options['general']['app_mode'] == "yes"){
		echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n";
		//echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"translucent black\">\n";
		//echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"translucent-black\">\n";
		echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"translucent\">\n";
		if(strlen($websitez_options['images']['android_icon']) > 0){
			echo "<link rel=\"apple-touch-icon-precomposed\" href=\"".$websitez_options['general']['android_icon']."\">\n";
		}
		if(strlen($websitez_options['images']['ios_icon']) > 0){
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"152x152\" rel=\"apple-touch-icon-precomposed\">\n";
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"144x144\" rel=\"apple-touch-icon-precomposed\">\n";
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"76x76\" rel=\"apple-touch-icon-precomposed\">\n";
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"72x72\" rel=\"apple-touch-icon-precomposed\">\n";
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"120x120\" rel=\"apple-touch-icon-precomposed\">";
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"114x114\" rel=\"apple-touch-icon-precomposed\">\n";
			echo "<link href=\"".$websitez_options['general']['ios_icon']."\" sizes=\"57x57\" rel=\"apple-touch-icon-precomposed\">\n";
		}
		if(strlen($websitez_options['images']['iphone_startup_screen']) > 0){
			echo "<link href=\"".$websitez_options['images']['iphone_startup_screen']."\" media=\"(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 1)\" rel=\"apple-touch-startup-image\">\n";
		}
		if(strlen($websitez_options['images']['retina_iphone_startup_screen']) > 0){
			echo "<link href=\"apple-touch-startup-image-640x920.png\" media=\"(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 2)\" rel=\"apple-touch-startup-image\">\n";
		}
		if(strlen($websitez_options['images']['5_iphone_startup_screen']) > 0){
			echo "<link href=\"".$websitez_options['images']['5_iphone_startup_screen']."\" media=\"(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)\" rel=\"apple-touch-startup-image\">\n";
		}
		if(strlen($websitez_options['images']['6_iphone_startup_screen']) > 0){
			echo "<link href=\"".$websitez_options['images']['6_iphone_startup_screen']."\" media=\"(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)\" rel=\"apple-touch-startup-image\">\n";
		}
	}
	echo "<style>body{ -webkit-text-size-adjust: 100%; }</style>";
}

/*
Attribution and ability to switch between mobile and full site
*/
function websitez_web_head_mobile(){
	echo "<script type='text/javascript'>\n
	function websitez_setMobileSite(){\n
		websitez_setCookie('websitez_mobile_detector','',-1);
		websitez_setCookie('websitez_mobile_detector_fullsite','',-1);
		window.location.reload();\n
	}\n
	function websitez_setCookie(c_name,value,expiredays){\n
		var exdate=new Date();\n
		exdate.setDate(exdate.getDate()+expiredays);\n
		document.cookie=c_name+ '=' +escape(value)+((expiredays==null) ? '' : ';expires='+exdate.toUTCString());\n
	}\n
	</script>\n";
}

/*
Returns an array of information about the device
*/
function websitez_detect_mobile_device(){
	global $wpdb;
	//Speaks for itself
  	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	//Check to see if query string variables exist
	if(isset($_GET['websitez-mobile'])){
		if(isset($_GET['websitez-mobile-type'])){
			$type = $_GET['websitez-mobile-type'];
		}else{
			$type = "1";
		}

		return array('status'=>'1','type'=>$type);
	}

	//Checks to see if this has already been detected as a device.
	$check = websitez_check_previous_detection($user_agent);

	if($check){
		return $check;
	}

	//Innocent until proven guilty
	$mobile_browser = false;
	//This can also be used to detect a mobile device
  	$accept = $_SERVER['HTTP_ACCEPT'];
	//Type of phone
	$mobile_browser_type = "0"; //0 - PC, 1 - Smart Phone, 2- Standard Phone
	$websitez_options = websitez_get_options();
	
	switch(true){
		case (preg_match('/ipad/i',$user_agent)||preg_match('/kindle/i',$user_agent)||preg_match('/nook/i',$user_agent)); //Tablets
			if($websitez_options['general']['display_to_tablet'] == 'yes'):
				$mobile_browser = true;
				$mobile_browser_type = "1"; //Smart Phone
			else:
				$mobile_browser = false;
				$mobile_browser_type = "0"; //Smart Phone
			endif;
    break;

		case (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent)); //iPhone or iPod
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/android/i',$user_agent)); //Android
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/opera mini/i',$user_agent)); //Opera Mini
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/blackberry/i',$user_agent)); //Blackberry
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/(series60|series 60)/i',$user_agent)); //Symbian OS
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent)); //Palm OS
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/(iris|3g_t|windows ce|opera mobi|iemobile)/i',$user_agent)); //Windows OS
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Phone
    break;

		case (preg_match('/(maemo|tablet|qt embedded|com2)/i',$user_agent)); //Nokia Tablet
      $mobile_browser = true;
			$mobile_browser_type = "1"; //Smart Device
    break;

		/*
		Now look for standard phones & mobile devices
		*/
		case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo|vnd.rim|wml|nitro|nintendo|wii|xbox|archos|openweb|mini|docomo)/i',$user_agent)); //Mix of standard phones
      $mobile_browser = true;
			$mobile_browser_type = "2"; //Standard Phone
    break;

		case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); //Any falling through the cracks
      $mobile_browser = true;
			$mobile_browser_type = "2"; //Standard Phone
    break;

		case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); //Any falling through the cracks
      $mobile_browser = true;
			$mobile_browser_type = "2"; //Standard Phone
    break;

		case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-'))); //Catch all
      $mobile_browser = true;
			$mobile_browser_type = "2"; //Standard Phone
    break;

		default;
			$mobile_browser = false;
			$mobile_browser_type = "0";
		break;
	}
	
	//Set a persistent client-side value to avoid having to detect again for this visitor
	websitez_set_previous_detection($mobile_browser,$mobile_browser_type,$user_agent);
	return array('status'=>$mobile_browser,'type'=>$mobile_browser_type);
}

function websitez_do_filter(){
	$websitez_options = websitez_get_options();
	$data = array(
		'wc-api' => 'am-software-api',
		'request' => 'status',
		'email' => get_option(WEBSITEZ_LICENSE_EMAIL_NAME),
		'licence_key' => get_option(WEBSITEZ_LICENSE_KEY_NAME),
		'product_id' => 'WP Mobile Detector',
		'instance' => $websitez_options['general']['password'],
		'software_version' => WEBSITEZ_PLUGIN_VERSION,
		'platform' => ''
	);
	$args = array(
		'timeout' => 15,
		'user-agent' => 'WordPress-Admin-Upgrade-Page'
	);
	$url = 'https://websitez.com/?'.http_build_query($data);
	$response = wp_remote_get( $url, $args );
	if(array_key_exists('body', $response) && strlen($response['body']) > 0){
		$result = json_decode($response['body'], true);
		if($result['status_check'] !== "active"){
			wp_clear_scheduled_hook( 'websitez_do_filter' );
			update_option(WEBSITEZ_LICENSE_KEY_NAME, '');
			update_option(WEBSITEZ_LICENSE_EMAIL_NAME, '');
		}
	}
}

/*
If it is a mobile device, lets try and remember to avoid having to detect it again
*/
function websitez_set_previous_detection($status,$type,$user_agent){
	if($status){
		//This is set to prevent caching mechanisms such as W3 total cache from caching the mobile page
		setcookie("websitez_is_mobile", "true", time()+3600, "/");
	}
	setcookie("websitez_mobile_detector", $status."|".$type."|".md5($user_agent), time()+3600, "/");
}

/*
Check to see if this mobile device has been previously detected
*/
function websitez_check_previous_detection($user_agent = null){
	if(isset($_COOKIE['websitez_mobile_detector_fullsite']) && isset($_COOKIE['websitez_mobile_detector'])){
		$obj = explode("|",$_COOKIE['websitez_mobile_detector']);
		//Check to see if their user agent has changed
		if($obj[2] != md5($user_agent)){
			//Their user agent hash doesn't match, which means they changed their
			//user agent somehow. Usually this is with a user agent switcher.
			return false;
		}else{
			//Returning a 0 will show the desktop version aka the 'fullsite'
			//This is executed if the user elected to view the 'fullsite' version
			return array('status'=>$obj[0],'type'=>'0');
		}
	}else if(isset($_COOKIE['websitez_mobile_detector'])){
		$obj = explode("|",$_COOKIE['websitez_mobile_detector']);
		//Check to see if their user agent has changed
		if($obj[2] != md5($user_agent)){
			//Their user agent hash doesn't match, which means they changed their user agent
			return false;
		}else{
			return array('status'=>$obj[0],'type'=>$obj[1]);
		}
	}else{
		return false;
	}
}

/*
Return the current themes in wp-content/themes
*/
function websitez_get_current_themes(){
	$websitez_preinstalled_templates = get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME);
	
	if($websitez_preinstalled_templates == "true"){
		$path = WEBSITEZ_PLUGIN_DIR.'/themes';
		return $wp_themes = websitez_get_themes($path);
	}else{
		if(!function_exists('get_themes'))
			return null;

		return $wp_themes = get_themes();
	}
}

/*
Information about the types of devices that can be detected
*/
function websitez_get_mobile_types(){
	return array(array('name'=>'Basic Mobile Device','option'=>WEBSITEZ_BASIC_THEME,'url_redirect'=>WEBSITEZ_BASIC_URL_REDIRECT),array('name'=>'Advanced Mobile Device','option'=>WEBSITEZ_ADVANCED_THEME,'url_redirect'=>WEBSITEZ_ADVANCED_URL_REDIRECT));
}

function websitez_get_themes($path = null, $only_mobile = false) {
	global $wp_themes, $wp_broken_themes, $wp_theme_directories;

	/*
	Register the default root as a theme directory 
	This was working, but occasionally would load the regular themes in the wp-content/themes
	Oddly enough this seemed to be sporadic.
	*/
	//register_theme_directory( $path );
	
	//Empty out the directory array and add the plugin dir
	if($only_mobile == true){
		$current_theme_directories = $wp_theme_directories;
		$wp_theme_directories = array($path);
	}

	if (!function_exists('search_theme_directories') || !$theme_files = search_theme_directories(true))
		return false;

	asort( $theme_files );

	$wp_themes = array();

	foreach ( (array) $theme_files as $theme_file ) {
		$theme_root = $theme_file['theme_root'];
		$theme_file = $theme_file['theme_file'];

		if ( !is_readable("$theme_root/$theme_file") ) {
			$wp_broken_themes[$theme_file] = array('Name' => $theme_file, 'Title' => $theme_file, 'Description' => __('File not readable.'));
			continue;
		}
		
		// Deprecated
		//$theme_data = get_theme_data("$theme_root/$theme_file");
		$parts = explode("/", $theme_file);
		$theme_data = wp_get_theme($parts[0],$theme_root);

		$name        = $theme_data['Name'];
		$title       = $theme_data['Title'];
		$description = wptexturize($theme_data['Description']);
		$version     = $theme_data['Version'];
		$author      = $theme_data['Author'];
		$template    = $theme_data['Template'];
		$stylesheet  = dirname($theme_file);

		$screenshot = false;
		foreach ( array('png', 'gif', 'jpg', 'jpeg') as $ext ) {
			if (file_exists("$theme_root/$stylesheet/screenshot.$ext")) {
				$screenshot = "screenshot.$ext";
				break;
			}
		}

		if ( empty($name) ) {
			$name = dirname($theme_file);
			$title = $name;
		}

		$parent_template = $template;

		if ( empty($template) ) {
			if ( file_exists("$theme_root/$stylesheet/index.php") )
				$template = $stylesheet;
			else
				continue;
		}

		$template = trim( $template );

		if ( !file_exists("$theme_root/$template/index.php") ) {
			$parent_dir = dirname(dirname($theme_file));
			if ( file_exists("$theme_root/$parent_dir/$template/index.php") ) {
				$template = "$parent_dir/$template";
				$template_directory = "$theme_root/$template";
			} else {
				/**
				 * The parent theme doesn't exist in the current theme's folder or sub folder
				 * so lets use the theme root for the parent template.
				 */
				if ( isset($theme_files[$template]) && file_exists( $theme_files[$template]['theme_root'] . "/$template/index.php" ) ) {
					$template_directory = $theme_files[$template]['theme_root'] . "/$template";
				} else {
					if ( empty( $parent_template) )
						$wp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => __('Template is missing.'), 'error' => 'no_template');
					else
						$wp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => sprintf( __('The parent theme is missing. Please install the "%s" parent theme.'),  $parent_template ), 'error' => 'no_parent', 'parent' => $parent_template );
					continue;
				}

			}
		} else {
			$template_directory = trim( $theme_root . '/' . $template );
		}

		$stylesheet_files = array();
		$template_files = array();

		$stylesheet_dir = @ dir("$theme_root/$stylesheet");
		if ( $stylesheet_dir ) {
			while ( ($file = $stylesheet_dir->read()) !== false ) {
				if ( !preg_match('|^\.+$|', $file) ) {
					if ( preg_match('|\.css$|', $file) )
						$stylesheet_files[] = "$theme_root/$stylesheet/$file";
					elseif ( preg_match('|\.php$|', $file) )
						$template_files[] = "$theme_root/$stylesheet/$file";
				}
			}
			@ $stylesheet_dir->close();
		}

		$template_dir = @ dir("$template_directory");
		if ( $template_dir ) {
			while ( ($file = $template_dir->read()) !== false ) {
				if ( preg_match('|^\.+$|', $file) )
					continue;
				if ( preg_match('|\.php$|', $file) ) {
					$template_files[] = "$template_directory/$file";
				} elseif ( is_dir("$template_directory/$file") ) {
					$template_subdir = @ dir("$template_directory/$file");
					if ( !$template_subdir )
						continue;
					while ( ($subfile = $template_subdir->read()) !== false ) {
						if ( preg_match('|^\.+$|', $subfile) )
							continue;
						if ( preg_match('|\.php$|', $subfile) )
							$template_files[] = "$template_directory/$file/$subfile";
					}
					@ $template_subdir->close();
				}
			}
			@ $template_dir->close();
		}

		//Make unique and remove duplicates when stylesheet and template are the same i.e. most themes
		$template_files = array_unique($template_files);
		$stylesheet_files = array_unique($stylesheet_files);

		$template_dir = dirname($template_files[0]);
		$stylesheet_dir = dirname($stylesheet_files[0]);

		if ( empty($template_dir) )
			$template_dir = '/';
		if ( empty($stylesheet_dir) )
			$stylesheet_dir = '/';

		// Check for theme name collision.  This occurs if a theme is copied to
		// a new theme directory and the theme header is not updated.  Whichever
		// theme is first keeps the name.  Subsequent themes get a suffix applied.
		// The Default and Classic themes always trump their pretenders.
		if ( isset($wp_themes[$name]) ) {
			if ( ('WordPress Default' == $name || 'WordPress Classic' == $name) &&
					 ('default' == $stylesheet || 'classic' == $stylesheet) ) {
				// If another theme has claimed to be one of our default themes, move
				// them aside.
				$suffix = $wp_themes[$name]['Stylesheet'];
				$new_name = "$name/$suffix";
				$wp_themes[$new_name] = $wp_themes[$name];
				$wp_themes[$new_name]['Name'] = $new_name;
			} else {
				$name = "$name/$stylesheet";
			}
		}

		$theme_roots[$stylesheet] = str_replace( WP_CONTENT_DIR, '', $theme_root );
		$wp_themes[$name] = array(
			'Name' => $name,
			'Title' => $title,
			'Description' => $description,
			'Author' => $author,
			'Author Name' => $theme_data['AuthorName'],
			'Author URI' => $theme_data['AuthorURI'],
			'Version' => $version,
			'Template' => $template,
			'Stylesheet' => $stylesheet,
			'Template Files' => $template_files,
			'Stylesheet Files' => $stylesheet_files,
			'Template Dir' => $template_dir,
			'Stylesheet Dir' => $stylesheet_dir,
			'Status' => $theme_data['Status'],
			'Screenshot' => $screenshot,
			'Tags' => $theme_data['Tags'],
			'Theme Root' => $theme_root,
			'Theme Root URI' => str_replace( WP_CONTENT_DIR, content_url(), $theme_root ),
		);
	}

	unset($theme_files);

	/* Store theme roots in the DB */
	if ( function_exists('get_site_transient') && get_site_transient( 'theme_roots' ) != $theme_roots )
		set_site_transient( 'theme_roots', $theme_roots, 7200 ); // cache for two hours
	unset($theme_roots);

	/* Resolve theme dependencies. */
	$theme_names = array_keys( $wp_themes );
	foreach ( (array) $theme_names as $theme_name ) {
		$wp_themes[$theme_name]['Parent Theme'] = '';
		if ( $wp_themes[$theme_name]['Stylesheet'] != $wp_themes[$theme_name]['Template'] ) {
			foreach ( (array) $theme_names as $parent_theme_name ) {
				if ( ($wp_themes[$parent_theme_name]['Stylesheet'] == $wp_themes[$parent_theme_name]['Template']) && ($wp_themes[$parent_theme_name]['Template'] == $wp_themes[$theme_name]['Template']) ) {
					$wp_themes[$theme_name]['Parent Theme'] = $wp_themes[$parent_theme_name]['Name'];
					break;
				}
			}
		}
	}
	
	//Empty out the directory array and add the plugin dir
	if($only_mobile == true){
		$wp_theme_directories = $current_theme_directories;
	}
	
	return $wp_themes;
}

/*
Perform a remote request with support for caching the result
*/
function websitez_remote_request($host,$path = '',$cache = false,$cacheTime = 3600){
	$hash = "wz_".md5($host."?".$path);
	if($cache == true){
		if ( function_exists('get_site_transient') ){
			$hash_data = get_site_transient( $hash );
			if($hash_data){
				return $hash_data;
			}
		}
	}
	$response = wp_remote_get($host."?".$path);
	if(is_array($response)){
		if($cache == true){
			if ( function_exists('get_site_transient') ){
				set_site_transient( $hash, $response['body'], $cacheTime );
			}
		}
		return $response['body'];
	}
	
	return '';
}
?>