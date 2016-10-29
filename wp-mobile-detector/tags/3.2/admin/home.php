<?php
// TODO

?>
<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php	
?>
<div class="wrap">
	<div style="float: right;">
	<?php if(websitez_is_paid() != true) { ?>
		<a href="admin.php?page=websitez_upgrade" class="button button-primary"><?php _e('Upgrade to PRO','wp-mobile-detector'); ?></a>
	<?php }else{ ?>
		<?php _e('Your PRO license key:','wp-mobile-detector'); ?> <strong><?php echo get_option(WEBSITEZ_LICENSE_KEY_NAME); ?></strong><br><small><a href="admin.php?page=websitez_themes&license=delete"><?php _e('remove license key','wp-mobile-detector'); ?></a></small>
	<?php } ?>
	</div>
	
	<h1><?php echo WEBSITEZ_PLUGIN_NAME." <small>v".WEBSITEZ_PLUGIN_VERSION."</small>"; ?></h1>
	
	<table class="" width="100%">
		<tbody>
			<tr>
				<td width="70%" valign="top">
					
					<table class="widefat">
						<tbody>
							<tr>
								<td style="padding: 0px;">
									<?php echo websitez_remote_request("http://websitez.com/api/v3/home.php","site=".urlencode(get_bloginfo('url')),false,86400); ?>
								</td>
							</tr>
						</tbody>
					</table>
					
				</td>
				<td width="30%" valign="top">
					
					<table class="widefat">
						<thead>
							<tr>
								<th><?php _e('Quick Links','wp-mobile-detector'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<ul>
										<li>
											<a href="http://websitez.com/wp-mobile-detector-guide/"><?php _e('WP Mobile Detector Guide','wp-mobile-detector'); ?></a>
										</li>
										<li>
											<a href="http://twitter.com/websitezcom"><?php _e('WP Mobile Detector on Twitter','wp-mobile-detector'); ?></a>
										</li>
										<li>
											<a href="http://websitez.com/blog/"><?php _e('WP Mobile Detector Blog','wp-mobile-detector'); ?></a>
										</li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
					
					<div style="max-height: 500px; overflow: scroll;">
						<table class="widefat" style="margin-top: 3px;">
							<thead>
								<tr>
									<th><?php _e('WP Mobile Detector News','wp-mobile-detector'); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<?php
										$feed = simplexml_load_string(websitez_remote_request('http://websitez.com/feed/','',true,86400));
										?>
										<ul>
											<?php 
											if(is_object($feed)){
												foreach($feed->channel->item as $item){ ?>
												<li>
													<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a><br>
													<small><?php echo date("F j, Y", strtotime($item->pubDate)); ?></small><br>
													<?php echo substr($item->description, 0, 75)."..."; ?>
												</li>
												<?php 
												}
											} ?>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
				</td>
			</tr>
		</tbody>
	</table>
</div>
<center>
	<small>Add your <a href="http://wordpress.org/support/view/plugin-reviews/wp-mobile-detector#postform">★★★★★</a> rating for <a href="http://wordpress.org/plugins/wp-mobile-detector/">WP Mobile Detector</a>.</small>
</center>