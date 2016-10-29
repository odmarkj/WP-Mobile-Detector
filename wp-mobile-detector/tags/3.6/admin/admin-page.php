<?php
function websitez_save_options() {
	global $wpdb; // this is how you get access to the database
	$response = array("status" => "false");
	if ( current_user_can( 'manage_options' ) ) {
		$websitez_options = array();
		$path = WEBSITEZ_PLUGIN_DIR.'/themes';
		$themes_preinstalled = websitez_get_themes($path,true);
	
		$preinstalled_themes_update = false;
		foreach($themes_preinstalled as $k=>$v):
			if($v['Template']==$_POST['general']['selected_mobile_theme']){
				//If this is true, this is a theme located in the plugins folder
				//This value will tell the rest of the script to look in the plugin themes folder
				if(get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME)){
					update_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME, "true");
					$preinstalled_themes_update = true;
				}
			}
		endforeach;
	
		//If this is false, it means we're using a theme from the regular themes folder
		//and must tell the rest of the script not to change the theme folder location
		if($preinstalled_themes_update == false){
			if(get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME)){
				update_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME, "false");
			}
		}
		
		if(get_option(WEBSITEZ_ADVANCED_THEME))
			update_option(WEBSITEZ_ADVANCED_THEME, $_POST['general']['selected_mobile_theme']);
		if(get_option(WEBSITEZ_BASIC_THEME))
			update_option(WEBSITEZ_BASIC_THEME, $_POST['general']['selected_mobile_theme']);
			
		$protected_keys = array();
		
		foreach($_POST as $k=>$v){
			if(is_array($v)){
				foreach($v as $key=>$value){
					$websitez_options[$k][$key] = $value;
				}
			}
		}
		$options = serialize($websitez_options);
		
		if(count($websitez_options) > 0){
			if(websitez_set_options($websitez_options)){
				$response['status'] = "true";
				$response['theme'] = $_POST['general']['selected_mobile_theme'];
			}else{
				$response['status'] = "false";
			}
		}else{
			$response['status'] = "false";
		}
	}
	echo json_encode($response);
	die();
}

//Make sure that we're displaying statistics according to the timezone
//set for each individual wordpress install
if(function_exists('date_default_timezone_set')){
	$id = get_option('timezone_string');
	if(strlen($id) > 0){
		date_default_timezone_set($id);
	}
}
add_action('init', 'websitez_admin_head_scripts');

function websitez_admin_head_scripts(){
	wp_enqueue_style('wzstyle',plugin_dir_url(__FILE__)."css/style.css");
	wp_enqueue_style('jpickerst',plugin_dir_url(__FILE__)."css/jpicker-1.1.5.min.css");
	wp_enqueue_script('jpicker', plugin_dir_url(__FILE__)."jpicker-1.1.5.min.js", array('jquery','jquery-ui-core','jquery-ui-sortable'), '0.1');
}

/*
Register the link on the left sidebar in the administration interface
*/
function websitez_configuration_menu(){
	add_menu_page( WEBSITEZ_PLUGIN_NAME, '<span style="font-size:12px;">'.WEBSITEZ_PLUGIN_NAME.'</span>', 'install_plugins', 'websitez_home', 'websitez_home_page',plugin_dir_url(__FILE__).'images/phone_icon_transparent_16x16.png');
	add_submenu_page( 'websitez_home', __('What\'s New', 'wp-mobile-detector'), __('What\'s New', 'wp-mobile-detector'), 'install_plugins', 'websitez_home', 'websitez_home_page' );
	add_submenu_page( 'websitez_home', __('Mobile Theme Settings', 'wp-mobile-detector'), __('Mobile Theme Settings', 'wp-mobile-detector'), 'install_plugins', 'websitez_themes', 'websitez_themes_page' );
	if(!websitez_is_paid()){
		add_submenu_page( 'websitez_home', __('Upgrade To PRO', 'wp-mobile-detector'), __('Upgrade To PRO', 'wp-mobile-detector'), 'install_plugins', 'websitez_upgrade', 'websitez_upgrade' );
	}
}

function current_mobile_theme_info($themes) {
	$current_theme_safe = get_current_mobile_theme();
	foreach($themes as $k=>$v):
		if($v['Template']==$current_theme_safe){
			$current_theme = $k;
			break;
		}else{
			$current_theme = ucwords(str_replace("-"," ",$current_theme_safe));
		}
	endforeach;

	$ct = new stdClass();
	$ct->name = $current_theme;
	$ct->title = $themes[$current_theme]['Title'];
	$ct->version = $themes[$current_theme]['Version'];
	$ct->parent_theme = $themes[$current_theme]['Parent Theme'];
	$ct->template_dir = $themes[$current_theme]['Template Dir'];
	$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
	$ct->template = $themes[$current_theme]['Template'];
	$ct->stylesheet = $themes[$current_theme]['Stylesheet'];
	$ct->screenshot = $themes[$current_theme]['Screenshot'];
	$ct->description = $themes[$current_theme]['Description'];
	$ct->author = $themes[$current_theme]['Author'];
	$ct->tags = $themes[$current_theme]['Tags'];
	$ct->theme_root = $themes[$current_theme]['Theme Root'];
	$ct->theme_root_uri = $themes[$current_theme]['Theme Root URI'];
	return $ct;
}

function get_current_mobile_theme(){
	$theme = get_option(WEBSITEZ_ADVANCED_THEME);
	return $theme;
}

function websitez_get_ordered_pages($ordered_pages){
	$final_pages = array();
	$not_selected_pages = array();
	$desired_pages = explode(",",$ordered_pages);
	$pages = get_pages('include='.$ordered_pages);
	$ex_pages = get_pages('exclude='.$ordered_pages);
	if($ordered_pages != ""){
		foreach($desired_pages as $dpage):
			foreach($pages as $page):
				if($dpage == $page->ID){
					$final_pages[] = $page;
				}
			endforeach;
		endforeach;
		
		return array_merge($final_pages,$ex_pages);
	}else{
		return $pages;
	}
}

function websitez_home_page(){
	include(dirname(__FILE__)."/home.php");
}

function websitez_themes_page(){
	include(dirname(__FILE__)."/themes.php");
}

function websitez_upgrade(){
	include(dirname(__FILE__)."/upgrade.php");
}
?>