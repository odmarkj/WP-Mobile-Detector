<?php if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
//websitez_check_for_update();

// Get the plugin options
$websitez_options = websitez_get_options();

if(isset($_GET['license']) && $_GET['license'] == "delete"){
	$data = array(
		'wc-api' => 'am-software-api',
		'request' => 'deactivation',
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
	$message = 'The plugin could not be downgraded.';
	if(array_key_exists('body', $response) && strlen($response['body']) > 0){
		$result = json_decode($response['body'], true);
		if($result['deactivated'] === true){
			wp_clear_scheduled_hook( 'websitez_do_filter' );
			update_option(WEBSITEZ_LICENSE_KEY_NAME, '');
			update_option(WEBSITEZ_LICENSE_EMAIL_NAME, '');
			$message = 'You have successfully downgraded the plugin!';
		}
	}
}

if(isset($_POST['disabled_plugins']) || isset($_GET['disabled_plugins'])){
	if(isset($_POST['disabled_plugins'])){
		$disabled_plugins = $_POST['disabled_plugins'];
	}else{
		$disabled_plugins = $websitez_options['general']['disable_plugins'];
	}
	$access_type = get_filesystem_method();
	if($access_type === 'direct'){
		$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
		if ( ! WP_Filesystem($creds) ) {
			$message = '<p>The permissions on the cache folder are incorrect. Please fix them to continue.</p>';
		}	
	
		$message = add_code_to_plugins($disabled_plugins);
	}else{
		$url = wp_nonce_url('admin.php?page=websitez_themes&disabled_plugins=true&tab=misc','bak-creds');
		if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
			return; // stop processing here
		}else{
			if ( ! WP_Filesystem($creds) ) {
				request_filesystem_credentials($url, '', true, false, null);
				return;
			}else{
				$message = add_code_to_plugins($disabled_plugins);
			}
		}
	}
}

if($_FILES){
	if(isset($_FILES['theLogo'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['theLogo'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['logo'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The logo was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['theBackground'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['theBackground'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['custom_background_image'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The background image was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['ios_icon'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['ios_icon'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['ios_icon'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The iOS icon was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['android_icon'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['android_icon'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['android_icon'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The Android icon was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['iphone_startup_screen'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['iphone_startup_screen'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['iphone_startup_screen'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The iPhone startup screen was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['retina_iphone_startup_screen'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['retina_iphone_startup_screen'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['retina_iphone_startup_screen'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The retina iPhone startup screen was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['5_iphone_startup_screen'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['5_iphone_startup_screen'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['5_iphone_startup_screen'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The iPhone 5 startup screen was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
	if(isset($_FILES['6_iphone_startup_screen'])){
		$overrides = array( 'test_form' => false);
		$file = wp_handle_upload($_FILES['6_iphone_startup_screen'], $overrides);
		if(array_key_exists('url', $file)){
			$websitez_options['images']['6_iphone_startup_screen'] = $file['url'];
			if(websitez_set_options($websitez_options)){
				$message .= "The iPhone 6 startup screen was uploaded successfully. ";
			}
		}else{
			$message .= "The permissions on your '".WP_CONTENT_DIR."/uploads' folder are incorrect for your WordPress installation. Please contact your website host to fix this. Once fixed, you can try again. ";
		}
	}
}

if($_POST){
	if(isset($_POST['theBackup'])){
		$overrides = array( 'test_form' => false);
		$content = json_decode(stripslashes($_POST['theBackup']), true);
		if(count($content) > 0){
			websitez_set_options($content);
			$message = "The backup was successfully restored.";
		}else{
			$message = "The backup was not formatted properly. Please contact support@websitez.com";
		}
	}
}

function add_code_to_plugins($disabled_plugins){
	global $wp_filesystem;
	$plugin_path = str_replace(ABSPATH, $wp_filesystem->abspath(), WP_PLUGIN_DIR);
	
	$updated_plugins = array();
	
	$message = "";
	
	$plugins = explode(",", $disabled_plugins);
	foreach($plugins as $plugin){
		if(strlen($plugin) > 0){
			$full_path = $plugin_path."/".$plugin;
			$parts = explode("/", $plugin);
			$content = $wp_filesystem->get_contents($full_path);
			$check_text = "apply_filters( '".$parts[0]."_disable', false )";
			if(stripos($content, $check_text) === false){
				$fnc = "<?php if ( apply_filters( '".$parts[0]."_disable', false ) === true ) { return; } ?>";
				$content = $fnc.$content;
				$s = $wp_filesystem->put_contents($full_path, $content);
				if(!$s){
					$message = "Could not disable this plugin: ".$parts[0];
				}else{
					$updated_plugins[] = $parts[0];
				}
			}else{
				$updated_plugins[] = $parts[0];
			}
		}
	}
	
	if(count($updated_plugins) > 0 && strlen($message) == 0){
		$message = "Plugins updated successfully.";
	}
	
	return $message;
}

if(isset($_GET['ftp'])){
	$cache = WEBSITEZ_PLUGIN_DIR.'/cache/';
	$permissions = substr(sprintf('%o', fileperms($cache)), -4);
	if($permissions != "0777"){
		$access_type = get_filesystem_method();
		if($access_type === 'direct'){
			$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
			if ( ! WP_Filesystem($creds) ) {
				echo '<p>We could not set the permissions on the cache folder for the WP Mobile Detector. The permissions need to be 777 in order for the plugin to work properly. Please set these permissions, and then refresh this page.</p><p>Execute the following command via SSH:<br><strong>chmod 777 '.$cache.'</strong></p><p>Connect via FTP to your site, navigate to this folder
"'.WP_CONTENT_DIR.'/plugins/wp-mobile-detector/". You\'ll see a "cache" folder. Right click and there should be an option to
set the permissions for the folder. You\'ll want to enter 777 and then click apply.</p>';
				return;
			}	
		
			$chmod = websitez_chmod_cache();
			if($chmod){
				$message = 'The permissions were set successfully.';
			}
		}else{
			$url = wp_nonce_url('admin.php?page=websitez_themes&ftp=true','ftp-creds');
			if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
				return; // stop processing here
			}else{
				if ( ! WP_Filesystem($creds) ) {
					request_filesystem_credentials($url, '', true, false, null);
					return;
				}else{
					$chmod = websitez_chmod_cache();
					if($chmod){
						$message = 'The permissions were set successfully.';
					}
				}
			}
		}
	}
}
if(isset($_GET['bak'])){
	$cache = WEBSITEZ_PLUGIN_DIR.'/cache/';
	$permissions = substr(sprintf('%o', fileperms($cache)), -4);
	if($permissions == "0777"){
		$access_type = get_filesystem_method();
		if($access_type === 'direct'){
			$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
			if ( ! WP_Filesystem($creds) ) {
				$message = 'The permissions on the cache folder are incorrect. Please fix them to continue.';
			}	
		
			$backup = websitez_generate_backup();
			if($backup){
				$file_url = WEBSITEZ_PLUGIN_DIR."/cache/backup.txt";
				if(file_exists($file_url)){
					$url = WEBSITEZ_PLUGIN_WEB_DIR."cache/backup.txt";
					$message = 'The download is ready, download it here: <a href="'.$url.'" target="_blank">right click and save as, or view it in your browser</a>';
				}
			}
		}else{
			$url = wp_nonce_url('admin.php?page=websitez_themes&bak=true','bak-creds');
			if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
				return; // stop processing here
			}else{
				if ( ! WP_Filesystem($creds) ) {
					request_filesystem_credentials($url, '', true, false, null);
					return;
				}else{
					$backup = websitez_generate_backup();
					if($backup){
						$file_url = WEBSITEZ_PLUGIN_DIR."/cache/backup.txt";
						if(file_exists($file_url)){
							$url = WEBSITEZ_PLUGIN_WEB_DIR."cache/backup.txt";
							$message = 'The download is ready, download it here: <a href="'.$url.'" target="_blank">right click and save as, or view it in your browser</a>';
						}
					}
				}
			}
		}
	}
}
if(isset($_GET['reset']) && $_GET['reset'] == "true"){
	websitez_set_options(websitez_default_settings());
	$message = 'The default settings have been applied. The page will now refresh.';
}

// Get themes
if(function_exists('get_allowed_themes')){
	//$themes_standard = get_allowed_themes();
	$themes_standard = wp_get_themes( array( 'allowed' => true ) );
}else{
	$themes_standard = array();
}
$path = WEBSITEZ_PLUGIN_DIR.'/themes';
$themes_preinstalled = websitez_get_themes($path,true);
$themes = array_merge($themes_standard,$themes_preinstalled);
// Get current theme
$ct = current_mobile_theme_info($themes);
?>
<style>
#saved, #loading{
	display: none;
}
#save_button a{
	background: #36bf00;
	color: #ffffff;
	border-color: #0074a2;
	box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
}
#save_button a:hover{
	background: #30a701;
}
.navigation{
	padding: 10px;
	background: #efefef;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-khtml-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #cccccc;
	margin-top: 20px;
	margin-bottom: 10px;
	text-align: center;
	border-bottom: 2px solid #333;
}
.tab .block{
	margin-top: 20px;
	margin-bottom: 20px;
	margin-left: 20px;
}
.disabled{
	pointer-events:none;
	color: #999999;
}
.tab .block label{
	font-weight: bold;
}
.tab .block small{
	color: #999999;
}
.tab h2{
	/*background: #efefef;*/
	padding: 0px 0px 0px 5px;
	margin: 0px 0px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-khtml-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #cccccc;
	font-size: 18px;
	font-style: italic;
	color: #666666;
	border-bottom: 2px solid #333;
}
.upgrade{
	font-size: 10px;
	background: #2ea2cc;
	color: #ffffff;
	float: right;
	margin: 10px 50px 0px 0px;
	display: block;
	padding: 0px 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-khtml-border-radius: 5px;
	border-radius: 5px;
}
.upgrade:hover{
	color: #efefef;
}
ul.children{
	margin-left: 10px;
}
/*
Default settings
*/
#theme-tab, #menu-tab, #ads-tab, #misc-tab, #stats-tab, #app-tab{
	display: none;
}
</style>
<?php if(isset($message) && strlen($message) > 0){ ?>
<div class="wrap">
	<table class="widefat">
		<tbody>
			<tr>
				<td>
					<?php echo $message; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php } ?>
<div class="wrap">
	<table class="widefat">
		<tbody>
			<tr>
				<td style="min-width: 397px; height: 100%;">
					<div style="width: 397px; z-index: 16; position: fixed; color: #ffffff; margin: 30px 0px 0px 0px; text-align: center;">
						<div id="saved">
							<img src="<?php echo plugin_dir_url(__FILE__)."images/green-check.png"; ?>">
						</div>
						
						<div id="loading">
							<img src="<?php echo plugin_dir_url(__FILE__)."images/loading-white.gif"; ?>">
						</div>
						
						<div id="save_button">
							<a href="#" id="save" class="button"><?php _e('Save Changes','wp-mobile-detector'); ?></a>
						</div>
					</div>
					<div id="intro" style="width: 397px; z-index: 17; position: fixed; color: #efefef; margin: 70px 0px 0px 0px; text-align: center; display: none;"></div>
					<div class="iphone6">
						<iframe id="websitez-preview" name="websitez-preview" src="<?php echo get_bloginfo('url');?>/?websitez-mobile=1&uid=<?php echo mt_rand(1,100000); ?>" frameborder="0" class="iphone6-box" scrolling="auto"></iframe>
					</div>
				</td>
				<td width="100%" height="100%">
					
					<!-- Settings/Themes Area -->
					
					<h1 style="margin-top: 10px; line-height: 1em;"><?php _e('Customize Your Mobile Website', 'wp-mobile-detector'); ?></h1>
					
					<p><?php _e('This page allows you to customize the look and feel for your entire mobile website.','wp-mobile-detector'); ?></p>
					
					<div class="navigation">
						<a href="#" id="general" class="nav button button-primary"><?php _e('General','wp-mobile-detector'); ?></a>
						<a href="#" id="theme" class="nav button"><?php _e('Theme','wp-mobile-detector'); ?></a>
						<a href="#" id="menu" class="nav button"><?php _e('Menu','wp-mobile-detector'); ?></a>
						<a href="#" id="ads" class="nav button"><?php _e('Ads','wp-mobile-detector'); ?></a>
						<a href="#" id="stats" class="nav button"><?php _e('Stats','wp-mobile-detector'); ?></a>
						<a href="#" id="app" class="nav button"><?php _e('App','wp-mobile-detector'); ?></a>
						<a href="#" id="misc" class="nav button"><?php _e('Misc','wp-mobile-detector'); ?></a>
					</div>
					
					<div id="general-tab" class="tab">
						
						<h2><?php _e('General','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Select the home page for mobile visitors:','wp-mobile-detector'); ?></label>
							<div>
								<select id="mobile_home_page" onchange="__wza.new_mobile_home_page = true; __wza.save();">
									<option value=""><?php _e('WordPress Default','wp-mobile-detector'); ?></option>
									<?php
									$pages = get_pages(array());
									foreach($pages as $page){
										echo "<option value='".$page->ID."'".($page->ID == $websitez_options['general']['mobile_home_page'] ? ' selected' : '').">".$page->post_title."</option>";
									}	
									?>
								</select>
								<div>
									<small><?php _e('This gives you the ability to change the home page only for mobile visitors. Please note that page templates from your desktop theme are not available.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<div class="block">
							<label<?php _e('>How many posts should be shown per page?','wp-mobile-detector'); ?></label>
							<div>
								<input type="text" id="posts_per_page" value="<?php echo $websitez_options['general']['posts_per_page']; ?>" />
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Change your header text for your mobile website:','wp-mobile-detector'); ?></label>
							<div>
								<input type="text" id="mobile_title" value="<?php echo $websitez_options['general']['mobile_title']; ?>" />
								<div>
									<small><?php _e('Sometimes a really long header can cause issues on the smaller screen of a mobile device.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Show this mobile theme to tablet devices?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="display_to_tablet" value="yes"<?php echo ($websitez_options['general']['display_to_tablet'] == "yes" ? ' checked' : ''); ?>>
							</div>
						</div>
						
						<h2><?php _e('Settings','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Disable this plugin?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="disable_plugin" value="true"<?php echo ($websitez_options['general']['disable_plugin'] == "true" ? ' checked' : ''); ?>>
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Redirect mobile visitors to a specific URL?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="redirect_mobile_visitors" value="yes"<?php echo (strlen($websitez_options['general']['redirect_mobile_visitors_website']) > 0 ? ' checked' : ''); ?>>
								<div id="redirect_options" class="block" style="<?php echo (strlen($websitez_options['general']['redirect_mobile_visitors_website']) == 0 ? 'display: none;' : ''); ?>">
									<label><?php _e('Enter the Website URL to redirect mobile visitors to:','wp-mobile-detector'); ?></label>
									<div>
										<input type="text" id="redirect_mobile_visitors_website" value="<?php echo $websitez_options['general']['redirect_mobile_visitors_website']; ?>">
									</div>
								</div>
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Enable mobile statistics?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="record_stats" value="true"<?php echo ($websitez_options['general']['record_stats'] == "true" ? ' checked' : ''); ?>>
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Display "Powered by WP Mobile Detector" in the footer?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="show_attribution" value="yes"<?php echo ($websitez_options['general']['show_attribution'] == "yes" ? ' checked' : ''); ?>>
							</div>
						</div>
						
						<h2><?php _e('Social Media','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Do you have a Twitter profile?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="twitter" value="yes"<?php echo (strlen($websitez_options['general']['twitter_username']) > 0 ? ' checked' : ''); ?>>
								<div id="twitter-url" style="<?php echo (strlen($websitez_options['general']['twitter_username']) == 0 ? 'display: none;' : ''); ?>">
									<input type="text" id="twitter_username" placeholder="Your Twitter username..." value="<?php echo $websitez_options['general']['twitter_username']; ?>">
								</div>
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Do you have a Facebook profile?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="facebook" value="yes"<?php echo (strlen($websitez_options['general']['facebook_url']) > 0 ? ' checked' : ''); ?>>
								<div id="facebook-url" style="<?php echo (strlen($websitez_options['general']['facebook_url']) == 0 ? 'display: none;' : ''); ?>">
									<input type="text" id="facebook_url" placeholder="Your Facebook profile link..." value="<?php echo $websitez_options['general']['facebook_url']; ?>">
								</div>
							</div>
						</div>
						
						<h2><?php _e('Sharing','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Enable sharing plugin?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="sharing" <?php echo (strlen($websitez_options['theme']['sharing_icons']) > 0 ? ' checked' : ''); ?>>
								<small><?php _e('This will place a small button fixed on the screen to allow the visitor to easily share your webpages.','wp-mobile-detector'); ?></small>
								<div id="sharing-options" style="<?php echo (strlen($websitez_options['theme']['sharing_icons']) == 0 ? 'display: none;' : ''); ?>">
									<?php 	
									$icons = array("facebook.png","twitter.png","delicious.png","google.png","linkedin.png","reddit.png","stumbleupon.png","email.png"); ?>
									<label><?php _e('Please select the icons you would like shown to the visitor.','wp-mobile-detector'); ?></label>
									<div class="icons">
										<? foreach($icons as $icon){ ?>
										<div style="width: 60px; margin-right: 20px; margin-bottom: 10px; float: left;"><input type="checkbox" class="sharing-icons" value="<?php echo $icon; ?>"<?php echo (stripos($websitez_options['theme']['sharing_icons'], $icon) !== false ? ' checked' : ''); ?>> <img src="<?php echo WEBSITEZ_PLUGIN_WEB_DIR."admin/images/32x32/".$icon; ?>"></div>
										<?php } ?>
										<div style="clear: both;"></div>
									</div>
									<label><?php _e('Select one or more page types you would like the sharing plugin to show on.','wp-mobile-detector'); ?></label>
									<div class="share-page-types">
										<p>
											<input type="checkbox" id="sharing_home" value="yes"<?php echo ($websitez_options['theme']['sharing_home'] == "yes" ? ' checked' : ''); ?>> <?php _e('Home Page','wp-mobile-detector'); ?>
										</p>
										<p>
											<input type="checkbox" id="sharing_posts" value="yes"<?php echo ($websitez_options['theme']['sharing_posts'] == "yes" ? ' checked' : ''); ?>> <?php _e('Posts+','wp-mobile-detector'); ?>
										</p>
										<p>
											<input type="checkbox" id="sharing_pages" value="yes"<?php echo ($websitez_options['theme']['sharing_pages'] == "yes" ? ' checked' : ''); ?>> <?php _e('Pages','wp-mobile-detector'); ?>
										</p>
										<p>
											<input type="checkbox" id="sharing_categories" value="yes"<?php echo ($websitez_options['theme']['sharing_categories'] == "yes" ? ' checked' : ''); ?>> <?php _e('Categories','wp-mobile-detector'); ?>
										</p>
										<p>
											<input type="checkbox" id="sharing_archives" value="yes"<?php echo ($websitez_options['theme']['sharing_archives'] == "yes" ? ' checked' : ''); ?>> <?php _e('Archives','wp-mobile-detector'); ?>
										</p>
									</div>
									<div class="sharing-exclude">
										<label><?php _e('Exclude pages by entering their ID\'s separated by commas.','wp-mobile-detector'); ?></label>
										<textarea id="sharing_exclude" rows="3" cols="40"><?php echo $websitez_options['theme']['sharing_exclude']; ?></textarea>
										<p><small><?php _e('This will prevent the share plugin from being shown on these posts+/pages etc.','wp-mobile-detector'); ?></small></p>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- end general -->
					
					<div id="theme-tab" class="tab">
						
						<div class="block">
							<label><?php _e('Select your mobile theme:','wp-mobile-detector'); ?></label>
							<div>
								<select id="selected_mobile_theme" onchange="__wza.save();">
									<?php foreach($themes as $name => $mobile_theme){ ?>
										<option value="<?php echo $mobile_theme['Template']; ?>" <?php if($ct->name == $name) echo 'selected="selected"';?>><?php echo $mobile_theme['Name']; ?></option>
									<?php } ?>
								</select>
								<div>
									<small><?php _e('This is the mobile theme that will be used for mobile visitors. Any theme located in "'.WP_CONTENT_DIR.'/themes/" and "'.WP_CONTENT_DIR.'/plugins/wp-mobile-detector/themes" will show up here. Only the <strong>Amanda Mobile</strong>, <strong>WZ Mobile</strong>, <strong>Websitez Mobile</strong> and <strong>Corporate Mobile</strong> can be modified by the interactive theme editor.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<?php if($websitez_options['general']['selected_mobile_theme'] == "amanda-mobile"){ ?>
						
						<?php } // end Amanda Mobile ?>
						
						<h2><?php _e('Typography','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Select a standard font:','wp-mobile-detector'); ?></label>
							<div>
								<select id="font_family" onchange="__wza.save();">
									<?php
									$arr = array(__('Helvetica Neue','helvetica_neue'),__('Georgia','georgia'),__('Tahoma','tahoma'),__('Verdana','verdana'));
									foreach($arr as $font){
										echo '<option value="'.$font.'"'.($websitez_options['general']['font_family'] == $font ? " selected" : "").'>'.$font.'</option>';
									}
									?>
								</select>
								<div>
									<small><?php _e('This is the font that will be used for the mobile webpage.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<h2><?php _e('Select The Colors','wp-mobile-detector'); ?></h2>
						<?php
						foreach($websitez_options['colors'] as $k=>$v){
						?>
						<div class="block">
							<?php
							if(websitez_is_paid() != true){
								echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
							}
							?>
							<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
								<label>
									<?php
									if($k == "custom_color_light")
										_e("Top Header Color",'wp-mobile-detector');
									else if($k == "custom_color_medium_light")
										_e("Content Area Accent","wp-mobile-detector");
									else if($k == "custom_color_dark")
										_e("Header & Post Color","wp-mobile-detector");
									else if($k == "default_link_color")
										_e("Standard Link Color","wp-mobile-detector");
									else if($k == "custom_post_background")
										_e("Post Background","wp-mobile-detector");
									else if($k == "custom_header_logo")
										_e("Logo Text Font Color","wp-mobile-detector");
									?>
								</label>
								<div>
									<input type='text' class='Multiple' id='<?php echo $k; ?>' value='<?php echo $v; ?>'>
								</div>
							</div>
						</div>
						<?php
						}
						?>
						<h2><?php _e('Custom Styles','wp-mobile-detector'); ?></h2>
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label><?php _e('Add custom styles to the mobile theme here.','wp-mobile-detector'); ?></label>
							<div>
								<textarea id="custom_styles" rows="5" cols="50"><?php echo stripslashes($websitez_options['general']['custom_styles']) ?></textarea>
								<div>
									<small><?php _e('This can be used to quickly modify the style of your mobile theme without having to edit the theme files. Do not include the style tags. It may be necessary to include "!important" in your styles to have them work properly from here.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<h2><?php _e('Customize The Graphics','wp-mobile-detector'); ?></h2>
						
						<?php
						if(isset($_GET['up'])){
							if($_GET['up'] == "permissions"){
								echo "<p style='color: #ff0000;'>The image could not be saved. This is most likely due to having incorrect permissions on the '".WP_CONTENT_DIR."/uploads' folder. Please set permissions to 777 for this folder and upload your image again.</p>";
							}
						}
						?>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('logo',$websitez_options['images']) && strlen($websitez_options['images']['logo']) > 0){
						?>
						<center>
							<img id='the_logo' src='<?php echo $websitez_options['images']['logo']; ?>' style="max-width: 500px; max-height: 500px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the logo?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Upload a logo','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="theLogo"> <input type="submit" value="Upload">
								</form>
								<div>
									<small><?php _e('Upload a logo to be placed in the header of the mobile website.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<?php if($websitez_options['general']['selected_mobile_theme'] == "corporate-mobile"){ ?>
						
							<?php if(array_key_exists('custom_background_image',$websitez_options['images']) && strlen($websitez_options['images']['custom_background_image']) > 0){ ?>
								<center>
									<img name="custom_background_image" src="<?php echo $websitez_options['images']['custom_background_image']; ?>" style="max-width: 500px; max-height: 500px;">
									<p><small>
										<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the background image?','wp-mobile-detector'); ?></a>
									</small></p>
								</center>
							<?php } ?>
							
							<?php
							if(websitez_is_paid() != true){
								echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
							}
							?>
							<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
								<label>
									<?php _e('Upload a custom background image for the <strong>Corporate Mobile</strong> mobile theme.','wp-mobile-detector'); ?>
								</label>
								<div>
									<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
										<input type="file" name="theBackground"> <input type="submit" value="Upload">
									</form>
								</div>
							</div>
						<?php } ?>
						
						<?php
						$images = array("images/bg-transparent.gif","images/bg.gif","images/bg-grey-bar.gif","images/bg-blue-bar.gif","images/bg-red-bar.gif","images/bg-green-bar.gif","images/bg-blue-bar-horizontal.gif","images/bg-grey-bar-horizontal.gif","images/bg-wingding-d.gif","images/bg-light-blue-horizontal.png","images/bg-light-green-horizontal.png","images/bg-light-grey-solid.png","images/bg-light-red-solid.png","images/bg-light-green-solid.png","images/bg-black-solid.png","images/bg-dark-blue-solid.png","images/bg-medium-brown-solid.png","images/bg-medium-grey-solid.png","images/bg-light-grey-gradient.png","images/bg-reverse-grey.png","images/bg-square-grey.png","images/bg-diagonal-grey.png","images/bg-red-solid.png","images/bg-light-purple-solid.png","images/bg-grey-circle.png");
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Select a background image for your mobile website.','wp-mobile-detector'); ?>
							</label>
							<div>
								<table class="">
									<tbody>
										<tr>
										<?php foreach($images as $k => $image){ ?>
											<?php if($k != 0 && $k % 5 == 0){ ?>
											</tr>
											<tr>
											<?php } ?>
											<td>
												<input type="radio" name="custom_website_background" id="custom_website_background" value="<?php echo $image; ?>"<?php echo ($websitez_options['images']['custom_website_background'] == $image ? ' checked' : ''); ?>>
												<div style="width: 50px; height: 50px; background: url('<?php echo WEBSITEZ_PLUGIN_WEB_DIR."/themes/websitez-mobile/".$image; ?>'); border: 1px solid #666666;"></div>
											</td>
										<?php } ?>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<?php
						$images = array("1_blank.png","1_archive.png","1_article.png","1_chat.png","1_clock.png","1_email.png","1_puzzle.png","1_shirts.png","2_person.png","2_gears.png","2_rss.png","2_house.png","2_book.png","22_house.png","3_smiley.png","3_offer.png","3_fire.png","3_present.png","3_people.png","3_image.png","4_printer.png","4_star.png","5_heart.png","5_compass.png","5_refresh.png","5_bird.png","5_chip.png","6_camera.png");
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Select a header icon for your mobile website.','wp-mobile-detector'); ?>
							</label>
							<div>
								<table class="">
									<tbody>
										<tr>
										<?php foreach($images as $k => $image){ ?>
											<?php if($k != 0 && $k % 5 == 0){ ?>
											</tr>
											<tr>
											<?php } ?>
											<td>
												<input type="radio" id="header_left_icon" name="header_left_icon" value="images/ico/<?php echo $image; ?>"<?php echo ($websitez_options['images']['header_left_icon'] == "images/ico/".$image ? ' checked' : ''); ?>>
												<img src="<?php echo WEBSITEZ_PLUGIN_WEB_DIR."/themes/websitez-mobile/images/ico/".$image; ?>">
											</td>
										<?php } ?>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div><!-- end theme -->
					
					<div id="menu-tab" class="tab">
						<h2><?php _e('Customize The Menu','wp-mobile-detector'); ?></h2>
						
						<div class="block">
						
							<p><?php _e('Each of the options below can be dragged and dropped to change the order they appear on the mobile website.','wp-mobile-detector'); ?></p>
							
							<p><?php _e('Please utilize custom menus to show categories and pages. You can find this under the "Appearance -> Menus" section. All the menus created there will show up for selection here.','wp-mobile-detector'); ?></p>
						
						</div>
						
						<div id="menu_sort" class="">
							<?php
							$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
							$menu_order = explode(",",$websitez_options['sidebar']['menu_order']);
							foreach($menu_order as $menu){
								if($menu == "show_menu" || $menu == "show_menu_div"){
								?>
								<div class="block tabber">
									<?php
									if(websitez_is_paid() != true && strlen($menu) > 0){
										echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
									}?>
									<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
										<label><?php _e('Show custom menus?','wp-mobile-detector'); ?></label>
										<div>
											<input type="checkbox" id="show_menu" value="yes"<?php echo ($websitez_options['sidebar']['show_menu'] == "yes" ? " checked" : ""); ?>>
											<div id="show_menu_options" class="block" style="<?php echo ($websitez_options['sidebar']['show_menu'] != "yes" ? "display: none;" : ""); ?>">
												<a href="#" onclick="jQuery('#show_menu_options').append(jQuery('#show_menu_options .custom_menu').clone().removeClass('custom_menu').prepend('<a href=\'#\' onclick=\'jQuery(this).parent().remove(); return false;\' style=\'float: right;\'>delete</a>')); return false;">Add another custom menu?</a><br><br>
												<label><?php _e('Select from your existing menus from "Appearance -> Menus".','wp-mobile-detector'); ?></label>
												<?php $custom_menu_ids = explode(",", $websitez_options['sidebar']['custom_menu_ids']); ?>
												<?php foreach($custom_menu_ids as $k => $cmi){ ?>
												<div class="block <?php if($k == 0) echo "custom_menu"; ?>">
													<?php if($k != 0){ ?>
													<a href="#" onclick="jQuery(this).parent().remove(); return false;" style="float: right;">delete</a>
													<?php } ?>
													<select>
														<option value=""><?php _e('Please select one...','wp-mobile-detector'); ?></option>
														<?php foreach($menus as $menu){
															echo "<option value='".$menu->term_id."'".($cmi == $menu->term_id ? ' selected' : '').">".$menu->name."</option>\n";
														}?>
													</select>
												</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<?php }elseif($menu == "show_pages" || $menu == "show_pages_div"){ ?>
								<div class="block tabber">
									<?php
									if(websitez_is_paid() != true && strlen($menu) > 0){
										echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
									}?>
									<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
										<label><?php _e('Show pages on the menu?','wp-mobile-detector'); ?></label>
										<div>
											<input type="checkbox" id="show_pages" value="yes"<?php echo ($websitez_options['sidebar']['show_pages'] == "yes" ? " checked" : ""); ?>>
											<div id="pages_menu_options" class="block" style="<?php echo ($websitez_options['sidebar']['show_pages'] != "yes" ? "display: none;" : ""); ?>">
												<label><?php _e('Select which pages will show, and in which order they will show.','wp-mobile-detector'); ?></label>
												<p><a href="#" onclick="jQuery('input[name=\'show_pages_items_item\']').attr('checked', true); return false;">Select All</a> - <a href="#" onclick="jQuery('input[name=\'show_pages_items_item\']').attr('checked', false); return false;">Deselect All</a></p>
												<div>
													<?php
													$pages = websitez_get_ordered_pages($websitez_options['sidebar']['show_pages_items']);
													if(strlen($websitez_options['sidebar']['show_pages_items']) > 0){
														$selected_pages = explode(",",$websitez_options['sidebar']['show_pages_items']);
													}else{
														$selected_pages = array();
													}
													?>
													<ul>
													<?php foreach($pages as $page){ 
														echo "<li><input type='checkbox' id='show_pages_items_item' name='show_pages_items_item' value='".$page->ID."'".(in_array($page->ID, $selected_pages) || count($selected_pages) == 0 ? ' checked' : '')."> <a href='".$page->guid."' target='_blank'>".$page->post_title."</a></li>";
													} ?>
													</ul>
													<small><?php _e('If you would like to change the order, please disable this menu and use a custom menu to do so.','wp-mobile-detector'); ?></small>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php }elseif($menu == "show_categories" || $menu == "show_categories_div"){ ?>
								<div class="block tabber">
									<?php
									if(websitez_is_paid() != true && strlen($menu) > 0){
										echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
									}?>
									<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
										<label><?php _e('Show categories on the menu?','wp-mobile-detector'); ?></label>
										<div>
											<input type="checkbox" id="show_categories" value="yes"<?php echo ($websitez_options['sidebar']['show_categories'] == "yes" ? " checked" : ""); ?>>
											<div id="categories_menu_options" class="block" style="<?php echo ($websitez_options['sidebar']['show_categories'] != "yes" ? "display: none;" : ""); ?>">
												<label><?php _e('Select which categories will show, and in which order they will show.','wp-mobile-detector'); ?></label>
												<p><a href="#" onclick="jQuery('input[name=\'show_categories_items_item\']').attr('checked', true); return false;">Select All</a> - <a href="#" onclick="jQuery('input[name=\'show_categories_items_item\']').attr('checked', false); return false;">Deselect All</a></p>
												<div>
													<?php
													$categories = get_categories(array('hide_empty' => false));
													if(strlen($websitez_options['sidebar']['show_categories_items']) > 0){
														$selected_categories = explode(",",$websitez_options['sidebar']['show_categories_items']);
													}else{
														$selected_categories = array();
													}
													?>
													<ul>
													<?php foreach($categories as $category){ 
														echo "<li><input type='checkbox' id='show_categories_items_item' name='show_categories_items_item' value='".$category->cat_ID."'".(in_array($category->cat_ID, $selected_categories) || count($selected_categories) == 0 ? ' checked' : '').">".$category->cat_name."</li>";
													} ?>
													</ul>
													<small><?php _e('If you would like to change the order, please disable this menu and use a custom menu to do so.','wp-mobile-detector'); ?></small>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php }elseif($menu == "show_search" || $menu == "show_search_div"){ ?>
								<div class="block tabber">
									<?php
									if(websitez_is_paid() != true && strlen($menu) > 0){
										echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
									}?>
									<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
										<label><?php _e('Show the search box?','wp-mobile-detector'); ?></label>
										<div>
											<input type="checkbox" id="show_search" value="yes"<?php echo ($websitez_options['sidebar']['show_search'] == "yes" ? " checked" : ""); ?>>
										</div>
									</div>
								</div>
								<?php }elseif($menu == "show_meta" || $menu == "show_meta_div"){ ?>
								<div class="block tabber">
									<?php
									if(websitez_is_paid() != true && strlen($menu) > 0){
										echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
									}?>
									<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
										<label><?php _e('Show the meta information box?','wp-mobile-detector'); ?></label>
										<div>
											<input type="checkbox" id="show_meta" value="yes"<?php echo ($websitez_options['sidebar']['show_meta'] == "yes" ? " checked" : ""); ?>>
										</div>
									</div>
								</div>
								<?php } // endif ?>
							<?php } // endforeach ?>
						</div>
					</div> <!-- end menu -->
					
					<div id="ads-tab" class="tab">
						<h2><?php _e('Mobile Advertising','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Place an ad at the top of your mobile website?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="top_ad" value="yes"<?php echo (strlen($websitez_options['ads']['show_header_snippet']) > 0 ? ' checked' : ''); ?>>
								<div id="top_ad_options" class="block" style="<?php echo (strlen($websitez_options['ads']['show_header_snippet']) == 0 ? 'display: none;' : ''); ?>">
									<label><?php _e('Enter a snippet of HTML/JavaScript.','wp-mobile-detector'); ?></label>
									<div>
										<textarea id="show_header_snippet" rows="8" cols="50"><?php echo stripslashes($websitez_options['ads']['show_header_snippet']) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						
						<div class="block">
							<label><?php _e('Place an ad at the bottom of your mobile website?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="bottom_ad" value="yes"<?php echo (strlen($websitez_options['ads']['show_footer_snippet']) > 0 ? ' checked' : ''); ?>>
								<div id="bottom_ad_options" class="block" style="<?php echo (strlen($websitez_options['ads']['show_footer_snippet']) == 0 ? 'display: none;' : ''); ?>">
									<label><?php _e('Enter a snippet of HTML/JavaScript.','wp-mobile-detector'); ?></label>
									<div>
										<textarea id="show_footer_snippet" rows="8" cols="50"><?php echo stripslashes($websitez_options['ads']['show_footer_snippet']) ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- end ads -->
					
					<div id="stats-tab" class="tab">
						<h2><?php _e('Mobile Statistics','wp-mobile-detector'); ?></h2>
						
						<div class="block">
						
							<p><?php _e('The chart below shows the number of mobile visitors to your website based on the type of device they used. An <strong>advanced</strong> device has a modern browser (smart phone), a <strong>basic</strong> device has a simple browser (flip phone).','wp-mobile-detector'); ?></p>
							
							<center>
								<?php _e('Showing mobile statistics for:','wp-mobile-detector'); ?> <select name="type" class="theme_template" style="width: 200px;" onchange="window.location='<?php echo $_SERVER['SCRIPT_NAME'];?>?tab=stats&page=<?php echo $_GET['page'];?>&type='+this.value">
									<option value="today" <?php if($_GET['type'] == "today") echo "selected";?>><?php _e('Today','wp-mobile-detector'); ?></option>
									<option value="7day" <?php if($_GET['type'] == "7day") echo "selected";?>><?php _e('Last 7 Days','wp-mobile-detector'); ?></option>
									<option value="mtd" <?php if($_GET['type'] == "mtd") echo "selected";?>><?php _e('Month-To-Date','wp-mobile-detector'); ?></option>
								</select>
							</center>
							
							<div id="chart_div" style="text-align: center; width: 100%; height: 100%; min-width: 450px; min-height: 400px;"></div>
						
						</div>
					</div> <!-- end stats -->
					
					<div id="app-tab" class="tab">
						
						<div class="block">
							<label><?php _e('Enable App mode for your mobile website?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="app_mode" value="yes"<?php echo ($websitez_options['general']['app_mode'] == "yes" ? ' checked' : ''); ?>>
								<small><?php _e('This will make your website appear like an App when added to a home screen on iOS and Android devices.','wp-mobile-detector'); ?></small>
							</div>
						</div>
							
						<h2><?php _e('iOS','wp-mobile-detector'); ?></h2>
						
						<?php if(websitez_is_paid() != true){ ?>
						<a href="admin.php?page=websitez_upgrade" class="upgrade"><?php _e('PRO ONLY','wp-mobile-detector'); ?></a>
						<?php } ?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label><?php _e('Do you want to promote your iOS app?','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="ios_app" value="yes"<?php echo (strlen($websitez_options['general']['ios_app_id']) > 0 ? ' checked' : ''); ?>>
								<div id="ios_app_options" style="display: none;">
									<input type="text" id="ios_app_id" placeholder="App Store ID" value="<?php echo $websitez_options['general']['ios_app_id']; ?>">
									<div>
										<small><?php _e('Enter your app\'s','wp-mobile-detector'); ?> <a href="http://itunes.apple.com/linkmaker/" target="_blank">App Store ID</a>.</small>
									</div>
								</div>
							</div>
						</div>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('ios_icon',$websitez_options['images']) && strlen($websitez_options['images']['ios_icon']) > 0){
						?>
						<center>
							<img id='ios_icon' src='<?php echo $websitez_options['images']['ios_icon']; ?>' style="max-width: 180px; max-height: 180px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the iOS icon?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<div class="block">
							<label>
								<?php _e('Upload an iOS icon (180x180 PNG)','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="ios_icon"> <input type="submit" value="Upload">
								</form>
								<div>
									<small><?php _e('This is the image that will appear on the home screen.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('iphone_startup_screen',$websitez_options['images']) && strlen($websitez_options['images']['iphone_startup_screen']) > 0){
						?>
						<center>
							<img id='iphone_startup_screen' src='<?php echo $websitez_options['images']['iphone_startup_screen']; ?>' style="max-width: 320px; max-height: 460px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the iPhone startup screen?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Upload an image for the iPhone startup screen (320x460 PNG)','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="iphone_startup_screen"> <input type="submit" value="Upload">
								</form>
							</div>
						</div>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('retina_iphone_startup_screen',$websitez_options['images']) && strlen($websitez_options['images']['retina_iphone_startup_screen']) > 0){
						?>
						<center>
							<img id='retina_iphone_startup_screen' src='<?php echo $websitez_options['images']['retina_iphone_startup_screen']; ?>' style="max-width: 320px; max-height: 460px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the retina iPhone startup screen?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Upload an image for the retina iPhone startup screen (640x920 PNG)','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="retina_iphone_startup_screen"> <input type="submit" value="Upload">
								</form>
							</div>
						</div>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('5_iphone_startup_screen',$websitez_options['images']) && strlen($websitez_options['images']['5_iphone_startup_screen']) > 0){
						?>
						<center>
							<img id='5_iphone_startup_screen' src='<?php echo $websitez_options['images']['5_iphone_startup_screen']; ?>' style="max-width: 320px; max-height: 460px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the iPhone 5 startup screen?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Upload an image for the iPhone 5 startup screen (640x1096 PNG)','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="5_iphone_startup_screen"> <input type="submit" value="Upload">
								</form>
							</div>
						</div>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('6_iphone_startup_screen',$websitez_options['images']) && strlen($websitez_options['images']['6_iphone_startup_screen']) > 0){
						?>
						<center>
							<img id='6_iphone_startup_screen' src='<?php echo $websitez_options['images']['6_iphone_startup_screen']; ?>' style="max-width: 320px; max-height: 460px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the iPhone 6 startup screen?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="block<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
							<label>
								<?php _e('Upload an image for the iPhone 6 startup screen (750x1294 PNG)','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="6_iphone_startup_screen"> <input type="submit" value="Upload">
								</form>
							</div>
						</div>
						
						<h2><?php _e('Android','wp-mobile-detector'); ?></h2>
						
						<?php
						if(is_array($websitez_options['images']) && array_key_exists('android_icon',$websitez_options['images']) && strlen($websitez_options['images']['android_icon']) > 0){
						?>
						<center>
							<img id='android_icon' src='<?php echo $websitez_options['images']['android_icon']; ?>' style="max-width: 96px; max-height: 96px;">
							<p><small>
								<a href="#" onclick="jQuery(this).parent().parent().parent().remove(); __wza.save(); return false;"><?php _e('Delete the Android icon?','wp-mobile-detector'); ?></a>
							</small></p>
						</center>
						<?php
						}
						?>
						<div class="block">
							<label>
								<?php _e('Upload an Android icon (96x96)','wp-mobile-detector'); ?>
							</label>
							<div>
								<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
									<input type="file" name="android_icon"> <input type="submit" value="Upload">
								</form>
								<div>
									<small><?php _e('This is the image that will appear on the home screen.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						</div>
					</div> <!-- end app -->
					
					<div id="misc-tab" class="tab">
						<h2><?php _e('Theme Fine Tuning','wp-mobile-detector'); ?></h2>
						
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">PRO ONLY</a>';
						}
						?>
						<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
						
							<div class="block">
								<label><?php _e('Hide comments completely from the mobile visitors?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="no_comments" value="yes"<?php echo ($websitez_options['general']['no_comments'] == "yes" ? ' checked' : ''); ?>>
									<small><?php _e('This will remove all references to comments from the mobile website.','wp-mobile-detector'); ?></small>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Hide references to authors?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="no_authors" value="yes"<?php echo ($websitez_options['general']['no_authors'] == "yes" ? ' checked' : ''); ?>>
									<small><?php _e('This will remove all references to authors from the mobile website.','wp-mobile-detector'); ?></small>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Hide references to post/page creation dates?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="no_creation" value="yes"<?php echo ($websitez_options['general']['no_creation'] == "yes" ? ' checked' : ''); ?>>
									<small><?php _e('This will remove all references to creation dates from the mobile website.','wp-mobile-detector'); ?></small>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Hide references to categories?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="no_categories" value="yes"<?php echo ($websitez_options['general']['no_categories'] == "yes" ? ' checked' : ''); ?>>
									<small><?php _e('This will remove all references to categories from the mobile website.','wp-mobile-detector'); ?></small>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Hide references to tags?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="no_tags" value="yes"<?php echo ($websitez_options['general']['no_tags'] == "yes" ? ' checked' : ''); ?>>
									<small><?php _e('This will remove all references to tags from the mobile website.','wp-mobile-detector'); ?></small>
								</div>
							</div>
						
						</div>
						
						<h2><?php _e('Analytics','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Place HTML/JavaScript analytics code on the mobile website.','wp-mobile-detector'); ?></label>
							<div>
								<input type="checkbox" id="show_analytics" value="yes"<?php echo (strlen($websitez_options['analytics']['show_analytics_snippet']) > 0 ? ' checked' : ''); ?>>
								<div id="show_analytics_options" class="block" style="<?php echo (strlen($websitez_options['analytics']['show_analytics_snippet']) == 0 ? 'display: none;' : ''); ?>">
									<label><?php _e('Enter a snippet of HTML/JavaScript.','wp-mobile-detector'); ?></label>
									<div>
										<textarea id="show_analytics_snippet" rows="8" cols="50"><?php echo stripslashes($websitez_options['analytics']['show_analytics_snippet']) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						
						<h2><?php _e('Compatibility','wp-mobile-detector'); ?></h2>
						
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">'.__('PRO ONLY','pro_only').'</a>';
						}
						?>
						<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
						
							<div class="block">
								<label><?php _e('Disable active plugins for mobile visitors?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="disable_plugins_button" value="true"<?php echo (strlen($websitez_options['general']['disable_plugins']) > 0 ? ' checked' : ''); ?>>
									<div id="disable_plugins_options" class="block" style="<?php echo (strlen($websitez_options['general']['disable_plugins']) == 0 ? 'display: none;' : ''); ?>">
										<label><?php _e('Select the plugins you would like to disable for mobile visitors:','wp-mobile-detector'); ?></label>
										<div>
											<table class="">
												<tbody>
													<tr>
														<?php 
														$plugins = get_option('active_plugins');
														$i = 0;
														foreach($plugins as $k => $plugin){
															if(stripos($plugin, 'wp-mobile-detector') === false){
															$parts = explode("/", $plugin);
															if($i != 0 && $i % 5 == 0){ ?>
															</tr>
															<tr>
															<?php } ?>
															<td>
																<input type="checkbox" id="disabled_plugins" value="<?php echo $plugin; ?>"<?php echo (stripos($websitez_options['general']['disable_plugins'], $parts[0]) !== false ? " checked" : ""); ?>> <?php echo $parts[0]; ?>
															</td>
															<?php $i++; } ?>
														<?php } ?>
													</tr>
												</tbody>
											</table>
											<div>
												<small><?php _e('To disable a plugin, it may require your FTP credentials. You will be prompted for them if this is the case.','wp-mobile-detector'); ?></small>
											</div>
											<form action="" method="POST" id="disabled_plugins_form">
												<input type="hidden" name="disabled_plugins" value="<?php echo $websitez_options['general']['disable_plugins'];?>">
											</form>
										</div>
									</div>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Remove shortcodes from the mobile website?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="remove_sc" value="true"<?php echo (strlen($websitez_options['general']['remove_shortcodes']) > 0 ? ' checked' : ''); ?>>
									<div id="remove_shortcodes_options" class="block" style="<?php echo (strlen($websitez_options['general']['remove_shortcodes']) == 0 ? 'display: none;' : ''); ?>">
										<label><?php _e('Enter a comma separated list of short codes to remove.','wp-mobile-detector'); ?></label>
										<div>
											<textarea id="remove_shortcodes" rows="4" cols="50"><?php echo stripslashes($websitez_options['general']['remove_shortcodes']) ?></textarea>
											<div>
												<small><?php _e('If you see a shortcode on a mobile page, add it here, and it will be removed.','wp-mobile-detector'); ?></small>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Load the functions.php file from the current desktop theme?','wp-mobile-detector'); ?></label>
								<div>
									<input type="checkbox" id="load_desktop_functions" value="yes"<?php echo ($websitez_options['general']['load_desktop_functions'] == "yes" ? ' checked' : ''); ?>>
									<div>
										<small><?php _e('Shortcodes, filters, photo galleries, and many other features for your pages and posts may rely on your desktop theme. This feature attempts to make them work for your mobile website as well.','wp-mobile-detector'); ?></small>
									</div>
								</div>
								<div id="load_desktop_functions_options" class="block" style="<?php echo ($websitez_options['general']['load_desktop_functions'] != "yes" ? 'display: none;' : ''); ?>">
									<label><?php _e('Please select the method you would like to use to include the desktop functions file.','wp-mobile-detector'); ?></label>
									<div>
										<select id="load_desktop_functions_method">
											<option><?php _e('Direct','wp-mobile-detector'); ?></option>
											<option value="translate"<?php echo ($websitez_options['general']['load_desktop_functions_method'] == "translate" ? ' selected' : ''); ?>><?php _e('Translate','wp-mobile-detector'); ?></option>
										</select>
										<div>
											<small><?php _e('If it is a complex functions.php file, or you receive errors, please select the "Translate" method.','wp-mobile-detector'); ?></small>
										</div>
									</div>
								</div>
							</div>
						
						</div>
						
						<h2><?php _e('Backup','wp-mobile-detector'); ?></h2>
						
						<?php
						if(websitez_is_paid() != true){
							echo '<a href="admin.php?page=websitez_upgrade" class="upgrade">'.__('PRO ONLY','pro_only').'</a>';
						}
						?>
						<div class="<?php echo (websitez_is_paid() != true ? ' disabled' : ''); ?>">
						
							<div class="block">
								<label><?php _e('Download a backup of your settings right now.','wp-mobile-detector'); ?></label>
								<div>
									<p><?php _e('A prompt to a text file download will occur. Store this file in a safe and secure location.','wp-mobile-detector'); ?></p>
									<p><a href="admin.php?page=websitez_themes&bak=true" class="button"><?php _e('Download Now','wp-mobile-detector'); ?></a></p>
								</div>
							</div>
							
							<div class="block">
								<label><?php _e('Restore settings from the contents of a backup file.','wp-mobile-detector'); ?></label>
								<div>
									<form action="admin.php?page=websitez_themes" method="POST" enctype="multipart/form-data">
										<textarea name="theBackup" rows="4" cols="50"></textarea><br>
										<input type="submit" value="Upload" class="button">
									</form>
									<div>
										<small><?php _e('Copy and paste the contents of your backup file here.','wp-mobile-detector'); ?></small>
									</div>
								</div>
							</div>
						
						</div>
						
						<h2><?php _e('Defaults','wp-mobile-detector'); ?></h2>
						
						<div class="block">
							<label><?php _e('Reset the plugin with default settings.','wp-mobile-detector'); ?></label>
							<div>
								<p><?php _e('If you are having trouble with the plugin, sometimes it is helpful to reset to the default configuration for the plugin and then try your changes again.','wp-mobile-detector'); ?></p>
								<p><a href="admin.php?page=websitez_themes&reset=true" class="button" onclick="return confirm('<?php _e('Are you sure?','wp-mobile-detector'); ?>');"><?php _e('Reset WP Mobile Detector to Default Settings','wp-mobile-detector'); ?></a></p>
							</div>
						</div>
						
					</div> <!-- end misc -->
					
					<center>
						<small><?php _e('The actual appearance of your site may vary slightly.','wp-mobile-detector'); ?></small>
					</center>
					
					<center>
						<small><?php _e('Add your','wp-mobile-detector'); ?> <a href="http://wordpress.org/support/view/plugin-reviews/wp-mobile-detector#postform">★★★★★</a> <?php _e('rating for','wp-mobile-detector'); ?> <a href="http://wordpress.org/plugins/wp-mobile-detector/"><?php echo WEBSITEZ_PLUGIN_NAME; ?></a>.</small>
					</center>
					
					<!-- End Settings/Themes Area -->
					
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Date');
	<?php
	//Set some values
	$total_googlebot_visits = 0;
	$total_bing_bot_visits = 0;
	$total_basic_unique_visits = 0;
	$total_advanced_unique_visits = 0;
	$total_advanced_visits = 0;
	$total_basic_visits = 0;
	$visitors = array();
	if(isset($_GET['type']) && $_GET['type'] == "mtd"){
  		$report_title = "Mobile Visits Month To Date";
  		$end_num = date("j");
  		$length = $end_num-1;
  		$begin_num = "1";
  		$start_date = date("Y-m-1 00:00:00");
  		$end_date = date("Y-m-j 23:59:59");
  		for($i=$begin_num;$i<=$end_num;$i++){
  			$chart_this[$i] = array();
  		}
  	}else if(isset($_GET['type']) && $_GET['type'] == "7day"){
  		$report_title = "Mobile Visits Last 7 Days";
  		$length = 6;
  		$start_date = date("Y-m-j 00:00:00", strtotime("-".$length." days"));
  		$end_date = date("Y-m-j 23:59:59");
  		for($i=$length;$i>=0;$i--){
  			$chart_this[date("j", strtotime("-".$i." days"))] = array();
  		}
  	}else{
  		$report_title = "Mobile Visits Today";
  		$end_num = date("j");
  		$length = 0;
  		$begin_num = $end_num;
  		$start_date = date("Y-m-j 00:00:00", strtotime("-".$length." days"));
  		$end_date = date("Y-m-j 23:59:59");
  		$chart_this[$end_num] = array();
  	}

	$results = $wpdb->get_results("SELECT * FROM ".WEBSITEZ_STATS_TABLE." WHERE created_at BETWEEN '".$start_date."' AND '".$end_date."' ORDER BY created_at DESC");
	if(count($results) > 0){
		//Put each unique visitor into an array
		foreach($results as $ar){
			$data = unserialize($ar->data);
			if(array_key_exists($data['REMOTE_ADDR'],$visitors)){
				$visitors[$data['REMOTE_ADDR']]['visits'][] = $ar->created_at;
			}else{
				$visitors[$data['REMOTE_ADDR']] = array('type'=>$ar->device_type,'data'=>$data,'visits'=>array($ar->created_at));
			}
		}
	}
	//Put together an array to display in the chart below
	if(count($visitors) > 0){
		foreach($visitors as $unique_visit):
			$type = $unique_visit['type'];
			//Get visit total
			if($type==2)
				$total_basic_visits += count($unique_visit['visits']);
			else if($type==1)
				$total_advanced_visits += count($unique_visit['visits']);
		
			if(preg_match('/(googlebot\-mobile|googlebot mobile)/i',$unique_visit['data']['HTTP_USER_AGENT'])){
				$total_googlebot_visits++;
			}else if(preg_match('/(MSNBOT_Mobile|MSNBOT-Mobile|MSNBOT Mobile)/i',$unique_visit['data']['HTTP_USER_AGENT'])){
				$total_msnbot_visits++;
			}
			
			//Create the array to put into the chart
			if(count($unique_visit['visits']) > 0){
				foreach($unique_visit['visits'] as $unique_visit_date):
					$day = date("j", strtotime($unique_visit_date));
					if(!array_key_exists($day,$chart_this)){
						$chart_this[$day][$type] = 1;
						break;
					}else{
						$chart_this[$day][$type] = $chart_this[$day][$type] + 1;
						break;
					}
				endforeach;
			}
		endforeach;
	}
	//End visitor calculations
	?>
	data.addColumn('number', 'Advanced');
	data.addColumn('number', 'Basic');
	data.addRows(<?php echo count($chart_this);?>);
	<?php
	$j=0;
	if(count($chart_this) > 0){
		foreach($chart_this as $day=>$day_data):
			echo "data.setValue(".$j.", 0,'".$day."');\n";
			if($day_data[2])
				echo "data.setValue(".$j.", 2, ".$day_data[2].");\n";
			else
				echo "data.setValue(".$j.", 2, 0);\n";
			if($day_data[1])
				echo "data.setValue(".$j.", 1, ".$day_data[1].");\n";
			else
				echo "data.setValue(".$j.", 1, 0);\n";
			$total_basic_unique_visits += $day_data[2];
			$total_advanced_unique_visits += $day_data[1];
			$j++;
		endforeach;
	}
  	?>
  	var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
	chart.draw(data, {title: '<?php echo $report_title; ?>'});
}
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
var __wza = {
	new_mobile_home_page: false,
	disabled_plugins: '<?php echo $websitez_options['general']['disable_plugins']; ?>',
	iframe_url: '<?php bloginfo('url');?>/?websitez-mobile=1&uid=<?php echo mt_rand(1,100000); ?>',
	//mode: 'doNOTsave',
	save: function(){
		jQuery('#save_button').fadeOut('fast', function(){ 
			jQuery('#loading').fadeIn('fast');
		});
		var disabled_plugins_has_changed = false;
		var refresh = false;
		//Get the selected pages to show
		var show_pages_items = "";
		jQuery('input[id=show_pages_items_item]:checked').each(function(){show_pages_items += jQuery(this).val()+","});
		//Get the selected categories to show
		var show_categories_items = "";
		jQuery('input[id=show_categories_items_item]:checked').each(function(){show_categories_items += jQuery(this).val()+","});
		var disabled_plugins = "";
		jQuery('input[id=disabled_plugins]:checked').each(function(){disabled_plugins += jQuery(this).val()+","});
		if(disabled_plugins != __wza.disabled_plugins){
			// No need to update if no plugins are going to be disabled
			if(disabled_plugins.length > 0){
				disabled_plugins_has_changed = true;
			}
			jQuery('input[name=disabled_plugins]').val(disabled_plugins);
			__wza.disabled_plugins = disabled_plugins;
		}
		var sharing_icons = "";
		jQuery('input[class=sharing-icons]:checked').each(function(){sharing_icons += jQuery(this).val()+","});
		//Get the order of the menu items
		var menu_order = "";
		jQuery('#menu_sort .tabber input[type="checkbox"]').each(function(){menu_order += jQuery(this).attr("id")+",";});
		var custom_menus = "";
		jQuery('#show_menu_options select').each(function(){var val = jQuery(this).val(); if(val.length > 0){ custom_menus += val+",";}});
		var data = {
			action: 'websitez_options',
			general: {
				mobile_home_page: jQuery("#mobile_home_page").val(),
				selected_mobile_theme: jQuery("#selected_mobile_theme").val(),
				mobile_title: jQuery("#mobile_title").val(),
				posts_per_page: jQuery("#posts_per_page").val(),
				twitter_username: jQuery("#twitter_username").val(),
				twitter_username_action: jQuery("#twitter_username_action").val(),
				facebook_url: jQuery("#facebook_url").val(),
				display_to_tablet: jQuery("#display_to_tablet:checked").val(),
				disable_plugin: jQuery('#disable_plugin:checked').val(),
				record_stats: jQuery('#record_stats:checked').val(),
				show_attribution: jQuery('#show_attribution:checked').val(),
				redirect_mobile_visitors_website: jQuery('#redirect_mobile_visitors_website').val(),
				no_comments: jQuery('#no_comments:checked').val(),
				no_authors: jQuery('#no_authors:checked').val(),
				no_creation: jQuery('#no_creation:checked').val(),
				no_categories: jQuery('#no_categories:checked').val(),
				no_tags: jQuery('#no_tags:checked').val(),
				remove_shortcodes: jQuery('#remove_shortcodes').val(),
				ios_app_id: jQuery('#ios_app_id').val(),
				load_desktop_functions: jQuery('#load_desktop_functions:checked').val(),
				load_desktop_functions_method: jQuery('#load_desktop_functions_method').val(),
				disable_plugins: disabled_plugins,
				custom_styles: jQuery('#custom_styles').val(),
				font_family: jQuery('#font_family').val(),
			},
			analytics: {
				show_analytics: jQuery("#show_analytics:checked").val(),
				show_analytics_snippet: jQuery("#show_analytics_snippet").val()
			},
			ads: {
				show_header: jQuery("#top_ad:checked").val(),
				show_header_snippet: jQuery("#show_header_snippet").val(),
				show_footer: jQuery("#bottom_ad:checked").val(),
				show_footer_snippet: jQuery("#show_footer_snippet").val(),
			},
			colors: {
				custom_color_light : jQuery("#custom_color_light").val(),
				custom_color_medium_light : jQuery("#custom_color_medium_light").val(),
				custom_color_dark : jQuery("#custom_color_dark").val(),
				default_link_color : jQuery("#default_link_color").val(),
				custom_post_background : jQuery("#custom_post_background").val(),
				custom_header_logo : jQuery("#custom_header_logo").val()
			},
			images: {
				custom_website_background : jQuery('input[id=custom_website_background]:checked').val(),
				header_left_icon : jQuery('input[id=header_left_icon]:checked').val(),
				logo : jQuery("#the_logo").attr("src"),
				custom_background_image : jQuery("#custom_background_image").attr("src"),
				ios_icon : jQuery("#ios_icon").attr("src"),
				android_icon : jQuery("#android_icon").attr("src")
			},
			sidebar: {
				menu_order: menu_order,
				show_menu: jQuery("#show_menu:checked").val(),
				show_pages: jQuery("#show_pages:checked").val(),
				show_pages_items: show_pages_items,
				show_categories: jQuery("#show_categories:checked").val(),
				show_categories_items: show_categories_items,
				show_meta: jQuery("#show_meta:checked").val(),
				show_search: jQuery("#show_search:checked").val(),
				custom_nav_menu_id: jQuery('#custom_nav_menu_id').val(),
				custom_menu_ids: custom_menus
			},
			theme: {
				sharing_icons: sharing_icons,
				sharing_posts: jQuery("#sharing_posts:checked").val(),
				sharing_pages: jQuery("#sharing_pages:checked").val(),
				sharing_home: jQuery("#sharing_home:checked").val(),
				sharing_categories: jQuery("#sharing_categories:checked").val(),
				sharing_archives: jQuery("#sharing_archives:checked").val(),
				sharing_exclude: jQuery("#sharing_exclude").val()
			}
		};
		if(jQuery("#delete_logo:checked").val()){
			data.images.logo = "";
		}
		if(jQuery("#delete_background:checked").val()){
			data.images.custom_background_image = "";
		}
		jQuery.post(ajaxurl, data, function(response) {
			var r = JSON.parse(response);
			console.log('server response: '+response);
			if(r.status == "true"){
				console.log('Successful update.');
			}else{
				console.log('ERROR on update');
			}
			jQuery('#loading').fadeOut('fast', function(){
				if(disabled_plugins_has_changed == true){
					// The plugins that they wish to disable need to be verified
					// This is a bit of a hack because we may need FTP information.
					jQuery('#disabled_plugins_form').submit();
				}
				__wza.refreshIframe();
				jQuery("#saved").fadeIn().delay(1000).fadeOut('slow', function(){
					jQuery('#save_button').fadeIn('fast');
				});
			});
		});
		
		return false;
	},
	refreshIframe: function(){
		if(__wza.new_mobile_home_page === true){
			location.reload(true);
		}else{
			try {
			    document.getElementById('websitez-preview').contentDocument.location.reload(true);
			}catch(err) {
			    location.reload(true);
			}
		}
	},
	rand: function(limit){
		limit = (limit > 0 ? limit : 100000);
		return Math.floor((Math.random() * limit) + 1);
	}
}
jQuery('a.nav').click(function(){
	jQuery('a.nav').removeClass('button-primary');
	jQuery('.tab').hide();
});
jQuery('#general').click(function(){
	jQuery('#general-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#theme').click(function(){
	jQuery('#theme-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#menu').click(function(){
	jQuery('#menu-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#ads').click(function(){
	jQuery('#ads-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#stats').click(function(){
	jQuery('#stats-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#app').click(function(){
	jQuery('#app-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#misc').click(function(){
	jQuery('#misc-tab').show('slow');
	jQuery(this).addClass('button-primary');
	return false;
});
jQuery('#twitter').change(function(){
	jQuery('#twitter-url').toggle();
});
jQuery('#facebook').change(function(){
	jQuery('#facebook-url').toggle();
});
jQuery('#show_menu').change(function(){
	jQuery('#show_menu_options').toggle();
});
jQuery('#show_pages').change(function(){
	jQuery('#pages_menu_options').toggle();
});
jQuery('#show_categories').change(function(){
	jQuery('#categories_menu_options').toggle();
});
jQuery('#load_desktop_functions').click(function(){
	jQuery('#load_desktop_functions_options').toggle();
});
jQuery('#top_ad').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('#show_header_snippet').val('');
	}
	jQuery('#top_ad_options').toggle();
});
jQuery('#bottom_ad').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('#show_footer_snippet').val('');
	}
	jQuery('#bottom_ad_options').toggle();
});
jQuery('#redirect_mobile_visitors').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('#redirect_mobile_visitors_website').val('');
	}
	jQuery('#redirect_options').toggle();
});
jQuery('#show_analytics').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('#show_analytics_snippet').val('');
	}
	jQuery('#show_analytics_options').toggle();
});
jQuery('#ios_app').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('#ios_app_id').val('');
	}
	jQuery('#ios_app_options').toggle();
});
jQuery('#remove_sc').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('#remove_shortcodes').val('');
	}
	jQuery('#remove_shortcodes_options').toggle();
});
jQuery('#disable_plugins_button').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery('input[id=disabled_plugins]:checked').each(function(){jQuery(this).attr('checked', false)});
	}
	jQuery('#disable_plugins_options').toggle();
});
jQuery('#sharing').change(function(){
	if(!jQuery(this).is(':checked')){
		jQuery(this).parent().find('input[class=sharing-icons]:checked').each(function(){ jQuery(this).attr('checked', false)});
	}
	jQuery('#sharing-options').toggle();
});
jQuery('#save').click(function(){
	return __wza.save();
});
jQuery(document).ready(function () {
	<?php if($_GET['reset'] == "true"){ ?>
	window.location = "admin.php?page=websitez_themes";
	<?php } ?>
	var iframe = jQuery('#websitez-preview')[0];
	jQuery(iframe).load(function () { //The function below executes once the iframe has finished loading
		console.log('iframe load fired');
		doc = iframe.document || iframe.contentDocument || iframe.contentWindow && iframe.contentWindow.document || null;
		jQuery("a", doc ).each(
			function(i){
				var linker = jQuery(this).attr("href");
				if(linker && linker.length > 1){
					if(linker.indexOf("?") != -1){
						jQuery(this).attr("href", linker+"&websitez-mobile=1&uid="+__wza.rand());
					}else{
						jQuery(this).attr("href", linker+"?websitez-mobile=1&uid="+__wza.rand());
					}
				}
			}
		);
	});
	jQuery('.Multiple').jPicker({
		window: {
			position: {
				x: 'screenCenter',
				y: '500'
			}
		},
		images: {
			clientPath: '<?php plugin_dir_url(__FILE__);?>admin/images/'
		}
	},
	function(color,context){
		//update_color(this.id,color.val('all').hex);
		__wza.save();
	});
	jQuery("#menu_sort").sortable({update: function(){__wza.save();}});
	//jQuery("#pages_menu_options ul").sortable({update: function(){__wza.save();}});
	//jQuery("#categories_menu_options ul").sortable({update: function(){__wza.save();}});
	<?php if($_GET['tab'] == "stats"){ ?>
	jQuery('#stats').click();
	<?php }elseif($_GET['tab'] == "graphics"){ ?>
	jQuery('#graphics').click();
	<?php }elseif($_GET['tab'] == "misc"){ ?>
	jQuery('#misc').click();
	<?php } ?>
	jQuery('#intro').text('<?php _e('Interact With Your Live Mobile Website Below!','wp-mobile-detector'); ?>').delay(1000).fadeIn('slow').delay(3000).fadeOut('slow');
});
</script>