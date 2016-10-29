<?php if ( ! defined( 'ABSPATH' ) ) exit;
if(!websitez_is_paid()){
	if($_POST){
		$websitez_options = websitez_get_options();
		$data = array(
			'wc-api' => 'am-software-api',
			'request' => 'activation',
			'email' => $_POST['licence_email'],
			'licence_key' => $_POST['licence_key'],
			'product_id' => 'WP Mobile Detector',
			'software_version' => WEBSITEZ_PLUGIN_VERSION,
			'platform' => ''
		);
		$data['instance'] = websitez_gen_uuid();
		$args = array(
			'timeout' => 15,
			'user-agent' => 'WordPress-Admin-Upgrade-Page',
			'body' => $data
		);
		$url = 'https://websitez.com/';
		$message = 'The license key provided is invalid.';
		$response = wp_remote_post( $url, $args );
		if(array_key_exists('body', $response) && strlen($response['body']) > 0){
			$result = json_decode($response['body'], true);
			if($result['activated'] == true){
				$websitez_options['general']['password'] = $data['instance'];
				websitez_set_options($websitez_options);
				wp_schedule_event( time(), 'monthly', 'websitez_do_filter' );
				update_option(WEBSITEZ_LICENSE_KEY_NAME, $data['licence_key']);
				update_option(WEBSITEZ_LICENSE_EMAIL_NAME, $data['email']);
				$message = 'You have successfully upgraded the plugin!';
			}else{
				$message .= " ".$response['body']." | ".json_encode($data);
			}
		}else{
			if(is_object($response) && is_array($response->errors)){
				$message .= " ".json_encode($response->errors);
			}else{
				$message .= " The request failed.";
			}
			$message .= " The license key is: ".$_POST['licence_key']." and email is ".$_POST['licence_email'].".";
		}
	}
}
?>
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
	
	<table class="" cellspacing="5">
		<tbody>
			<?php if(!websitez_is_paid()){ ?>
			<tr>
				<td colspan="4">
					<table class="widefat">
						<tbody>
							<tr>
								<td width="50%">
									<p><strong><?php _e('Need to activate your install?</strong> Enter your WP Mobile Detector PRO license key here to unlock the PRO version. If you do not have a license key, you may purchase one here:','wp-mobile-detector');?> <a href="<?php echo PURCHASE_WEBSITEZ_PRO_LINK; ?>">WP Mobile Detector PRO</a></p>
								</td>
								<td width="50%">
									<form action="admin.php?page=websitez_upgrade" method="POST">
										<p>
											Activation E-Mail: <input type="text" name="licence_email" placeholder="Enter your activation email...">
										</p>
										<p>
											License Key: <input type="text" name="licence_key" placeholder="Enter your license key here...">
										</p>
										<p>
											<input type="submit" value="Validate License" class="button">
										</p>
									</form>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="4">
					<table class="widefat">
						<tbody>
							<tr>
								<td style="padding: 40px 0px;">
									<center>
										<img height="321" src="<?php echo plugin_dir_url(__FILE__); ?>/images/1websitez-mobile-page.jpg">
										<img height="321" src="<?php echo plugin_dir_url(__FILE__); ?>/images/4screenshot-11.jpg">
										<img height="321" src="<?php echo plugin_dir_url(__FILE__); ?>/images/5screenshot-4.jpg">
										<img height="321" src="<?php echo plugin_dir_url(__FILE__); ?>/images/3corporate-mobile.jpg">
									</center>
									
									<center>
										<h1 style="font-size: 45px; margin: 40px 0px 10px;">WP Mobile Detector <i>PRO</i></h1>
									</center>
									
									<center>
										<h3 style="margin-bottom: 20px;"><?php _e('Unlock all features instantly for one low price!','wp-mobile-detector'); ?></h3>
									</center>
									
									<center>
										<a href="http://websitez.com/?ref=wp_admin" class="button button-primary" style="font-size: 23px; height: 40px; line-height: 38px; padding: 0px 50px;"><?php _e('Learn More','wp-mobile-detector'); ?></a>
									</center>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td width="25%" valign="top">
					<table class="widefat" style="min-height: 250px;">
						<tbody>
							<tr>
								<td>
									<h3>11 WordPress Themes</h3>
									<p>Not only do we provide an unprecedented 11 mobile themes at no additional cost, many of which are the fastest and most efficient on the mobile web, but we allow you to add your very own mobile theme.</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td width="25%" valign="top">
					<table class="widefat" style="min-height: 250px;">
						<tbody>
							<tr>
								<td>
									<h3>Works on 5,000+ Mobile Devices</h3>
									<p>Holy cow! That's a lot of devices. In fact, no other WordPress mobile plugin supports more. Chances are pretty great that your mobile visitors will see your mobile site when they visit.</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td width="25%" valign="top">
					<table class="widefat" style="min-height: 250px;">
						<tbody>
							<tr>
								<td>
									<h3>Interactive Theme Editor</h3>
									<p>Make your mobile site scream your brand from the rooftops! Customize the look and feel of your mobile website by adding a logo, selecting colors, configuring the menu, adding analytics, and much more.</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td width="25%" valign="top">
					<table class="widefat" style="min-height: 250px;">
						<tbody>
							<tr>
								<td>
									<h3>Mobile Device Differentiation</h3>
									<p>You know that one guy who’s still using a Nokia flip phone? Well he deserves to see your super cool website too. So WP Mobile Detector knows the difference between an advanced “smart” device and a standard device.</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>