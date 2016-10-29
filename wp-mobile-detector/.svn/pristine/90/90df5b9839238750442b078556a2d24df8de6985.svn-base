<?
//Make sure that we're displaying statistics according to the timezone
//set for each individual wordpress install
if(function_exists('date_default_timezone_set'))
	date_default_timezone_set(get_option('timezone_string'));
add_action('admin_head', 'websitez_admin_head');

function websitez_admin_head()
{
	echo "<link rel='stylesheet' id='mobiledetector-css'  href='".plugin_dir_url(__FILE__)."css/style.css' type='text/css' media='all' />";
}

/*
Register the link on the left sidebar in the administration interface
*/
function websitez_configuration_menu(){
	add_menu_page( __( WEBSITEZ_PLUGIN_NAME, 'Websitez' ), __( '<span style="font-size:12px;">'.__(WEBSITEZ_PLUGIN_NAME).'</span>', 'Websitez' ), 8, 'websitez_config', 'websitez_configuration_page',plugin_dir_url(__FILE__).'images/phone_icon_16x16.png');
	add_submenu_page( 'websitez_config', __('Settings', 'Websitez'), __('Settings', 'Websitez'), 8, 'websitez_config', 'websitez_configuration_page' );
	add_submenu_page( 'websitez_config', __('Stats', 'Websitez'), __('Stats', 'Websitez'), 8, 'websitez_stats', 'websitez_stats_page' );
}

function websitez_stats_page(){
	global $wpdb, $websitez_plugin_description, $table_prefix, $websitez_free_version;
?>
	<div class="wrap">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="60%" valign="top">
					<h1><?php echo esc_html( __(WEBSITEZ_PLUGIN_NAME." - Statistics") ); ?></h1>
					<p><?php _e('View detailed mobile visitor statistics from users who visit your site from a mobile device.');?></p>
				</td>
				<td width="40%" valign="top" align="right" style="padding: 15px 15px 0px 0px">
					<?php _e(websitez_dynamic_offers_stats());?>
				</td>
			</tr>
		</table>
		<h2 style="padding-top: 0px;"><?php _e('Mobile Statistics')?></h2>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	  <script type="text/javascript">
	    google.load("visualization", "1", {packages:["corechart"]});
	    google.setOnLoadCallback(drawChart);
	    function drawChart() {
	      var data = new google.visualization.DataTable();
	      data.addColumn('string', 'Date');
	      <?
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
	      		$chart_this[date("m")."/".$i] = array();
	      	}
	      }else if(isset($_GET['type']) && $_GET['type'] == "7day"){
	      	$report_title = "Mobile Visits Last 7 Days";
	      	$length = 6;
	      	$start_date = date("Y-m-j 00:00:00", strtotime("-".$length." days"));
	      	$end_date = date("Y-m-j 23:59:59");
	      	for($i=$length;$i>=0;$i--){
	      		$chart_this[date("m/j", strtotime("-".$i." days"))] = array();
	      	}
	      }else{
	      	$report_title = "Mobile Visits Today";
	      	$end_num = date("j");
	      	$length = 0;
	      	$begin_num = $end_num;
	      	$start_date = date("Y-m-j 00:00:00", strtotime("-".$length." days"));
	      	$end_date = date("Y-m-j 23:59:59");
	      	$chart_this[date("m")."/".$end_num] = array();
	      }

				$results = $wpdb->get_results("SELECT * FROM ".WEBSITEZ_STATS_TABLE." WHERE created_at BETWEEN '".$start_date."' AND '".$end_date."' ORDER BY created_at DESC");
				if(count($results) > 0){
					//Put each unique visitor into an array
					foreach($results as $ar):
						$data = unserialize($ar->data);
						if(array_key_exists($data['REMOTE_ADDR'],$visitors)){
							$visitors[$data['REMOTE_ADDR']]['visits'][] = $ar->created_at;
						}else{
							$visitors[$data['REMOTE_ADDR']] = array('type'=>$ar->device_type,'data'=>$data,'visits'=>array($ar->created_at));
						}
					endforeach;
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
								$day = date("m/j", strtotime($unique_visit_date));
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
				data.addColumn('number', 'Advanced Mobile Device');
	      data.addColumn('number', 'Basic Mobile Device');
				data.addRows(<?=count($chart_this)?>);
				<?
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
	      //var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	      chart.draw(data, {width: 1000, height: 340, title: ''});
	    }
	  </script>
	  <table class="widefat post fixed" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column" scope="col" style="text-align: center; font-size: 13px;">
						<?php _e('Showing mobile statistics for:')?>
						<select name="type" class="theme_template" style="width: 200px;" onchange="window.location='<?=$_SERVER['SCRIPT_NAME']?>?page=<?=$_GET['page']?>&type='+this.value">
							<option value="today" <? if($_GET['type'] == "today") echo "selected";?>>Today</option>
							<option value="7day" <? if($_GET['type'] == "7day") echo "selected";?>>Last 7 Days</option>
							<option value="mtd" <? if($_GET['type'] == "mtd") echo "selected";?>>Month-To-Date</option>
						</select>
					</th>
				</tr>
			</thead>
			<tr valign="top" class="author-self status-publish iedit">
				<td>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20" align="center" style="padding-top: 5px;"><img src="<?= plugin_dir_url(__FILE__); ?>images/basic_phone_icon_16x16.gif"></td>
							<td><?php _e('<h3 style="margin: 0px 0px 10px;"><u>Basic Mobile Device</u></h3><p>Total Unique Visitors: '.$total_basic_unique_visits.'</p><p>Total Visits: '.$total_basic_visits.'</p>') ?></td>
							<td width="20" align="center" style="padding-top: 5px;"><img src="<?= plugin_dir_url(__FILE__); ?>images/phone_icon_16x16.png"></td>
							<td><?php _e('<h3 style="margin: 0px 0px 10px;"><u>Advanced Mobile Device</u></h3><p>Total Unique Visitors: '.$total_advanced_unique_visits.'</p><p>Total Visits: '.$total_advanced_visits.'</p>') ?></td>
							<td width="20" align="center" style="padding-top: 5px;"><img src="<?= plugin_dir_url(__FILE__); ?>images/icon_analytics_16x16.gif"></td>
							<td><?php _e('<h3 style="margin: 0px 0px 10px;"><u>Mobile Device Details</u></h3><p>Total Googlebot Mobile Visitors: '.$total_googlebot_visits.'</p><p>Total Bing Bot Mobile Visitors: '.$total_bing_bot_visits.'</p>') ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr valign="top" class="author-self status-publish iedit">
				<td>
					<div id="chart_div" style="text-align: center;"></div>
				</td>
			</tr>
		</table>
		<h2><?php _e('Unique Visitor Details')?></h2>
		<p><?php _e('Showing '.count($visitors).' visitors.')?></p>
		<table class="widefat post fixed" cellspacing="0" style="margin: 0px 0px;">
			<thead>
				<tr>
					<th width="50" class="manage-column" scope="col">
						<?php _e('Device')?>
					</th>
					<th width="50" class="manage-column" scope="col">
						<?php _e('Visits')?>
					</th>
					<th width="130" class="manage-column" scope="col">
						<?php _e('Last Visit')?>
					</th>
					<th width="130" class="manage-column" scope="col">
						<?php _e('IP')?>
					</th>
					<th class="manage-column" scope="col">
						<?php _e('User Agent')?>
					</th>
				</tr>
			</thead>
			<?
			if(count($visitors) > 0){
				foreach($visitors as $v):
				?>
				<tr valign="top" class="author-self status-publish iedit">
					<td style="padding-top: 5px;"><img src="<?= plugin_dir_url(__FILE__); ?>images/<? if($v['type'] == "2") echo "basic_phone_icon_16x16.gif"; else echo "phone_icon_16x16.png";?>"></td>
					<td><?php _e('<p>'.count($v['visits']).'</p>') ?></td>
					<td><?php _e('<p>'.date("Y-m-d H:i:s", strtotime($v['visits'][(count($v['visits'])-1)])).'</p>') ?></td>
					<td><?php _e('<p>'.$v['data']['REMOTE_ADDR'].'</p>') ?></td>
					<td><?php _e('<p>'.$v['data']['HTTP_USER_AGENT'].'</p>') ?></td>
				</tr>
				<?
				endforeach;
			}
			?>
		</table>
	</div>
	<div>
		<?
		//Get dynamic footer
		_e(websitez_dynamic_footer());
		?>
	</div>
<?
}

function websitez_configuration_page() 
{
	global $wpdb, $websitez_plugin_description, $table_prefix, $websitez_free_version;
?>
<div class="wrap">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="60%" valign="top">
				<h1><?php echo esc_html( __(WEBSITEZ_PLUGIN_NAME) ); ?></h1>
				<p><?php _e('Configure which theme to show to each mobile device.') ?></p>
			</td>
			<td width="40%" valign="top" align="right" style="padding: 15px 15px 0px 0px">
				<p><a href="http://ready.mobi/results.jsp?uri=<?=bloginfo('url')?>&ref=websitez-com-wp-mobile-detector" target="_blank" title="<?php _e('Check the mobile readiness of this website.') ?>"><img src="<?=plugin_dir_url(__FILE__).'images/check-mobile-readiness.jpg'?>" border="0" alt="<?php _e('Check the mobile readiness of this website.') ?>"></a></p>
				<?php _e(websitez_dynamic_offers());?>
			</td>
		</tr>
	</table>
	<div id="plugin-description" class="widefat alternate" style="margin:10px 0; padding:5px;background-color:#FFFEEB;">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20" align="center" style="padding-top: 5px;"><img src="<?= plugin_dir_url(__FILE__); ?>images/basic_phone_icon_16x16.gif"></td>
				<td><?php _e('<h3 style="margin: 0px 0px 10px;"><u>Basic Mobile Device</u></h3><p>The WP Mobile Detector will remove all images and advanced HTML  from being displayed on basic devices.</p>') ?></td>
			</tr>
			<tr>
				<td width="20" align="center" style="padding-top: 10px;"><img src="<?= plugin_dir_url(__FILE__); ?>images/phone_icon_16x16.png"></td>
				<td><?php _e('<h3 style="margin: 5px 0px 10px;"><u>Advanced Mobile Device</u></h3><p>The WP Mobile Detector will resize images that are too large to display on advanced mobile devices.</p>') ?></td>
			</tr>
		</table>
	</div>

<?php
	if(isset($_POST['action'])) {
		$field = $_POST['action'];
		$value = $_POST[$field];
		$url = $_POST['redirect_url'];
		$url_field = $_POST['url_field'];
		
		if(get_option($field)){
			if(update_option($field, $value)){
				$u = true;
			}else{
				$error_message = "The theme could not be saved.";
				$u = false;
			}
		}else{
			$error_message = "It appears that the plugin was not installed properly. The option to update was not found.";
			$u = false;
		}
		
		if($url != ''){
			if(update_option($url_field, $url)){
				$u = true;
			}else{
				$error_message = "The Redirect URL could not be updated.";
				$u = false;
			}
		}
		
		if($u)
			echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
		else
			echo '<div id="message" class="updated fade"><p><strong>Error saving settings.</strong></p><p>'.$error_message.'</p></div>';
	}else if(isset($_POST['record_stats'])){
		$value = $_POST['record_stats'];
		
		if(get_option(WEBSITEZ_RECORD_STATS_NAME)){
			if(update_option(WEBSITEZ_RECORD_STATS_NAME, $value)){
				$u = true;
			}else{
				$u = false;
			}
		}else{
			$u = false;
		}
		
		if($u)
			echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
		else
			echo '<div id="message" class="updated fade"><p><strong>Error saving settings.</strong></p></div>';
	}else if(isset($_POST['use_preinstalled_themes'])){
		$value = $_POST['use_preinstalled_themes'];
		
		if(get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME)){
			if(update_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME, $value)){
				$u = true;
			}else{
				$u = false;
			}
		}else{
			$u = false;
		}
		
		if($u)
			echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
		else
			echo '<div id="message" class="updated fade"><p><strong>Error saving settings.</strong></p></div>';
		
	}
	
	//Now that the settings are saved, get the themes
	$current_themes_installed = websitez_get_current_themes();
?>
		<table class="widefat post fixed" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column" scope="col" width="180">Phone Type</th>
					<th class="manage-column" scope="col" width="250">Mobile Theme</th>
					<?/*<th class="manage-column" scope="col" width="370">Redirect URL</th>*/?>
					<th class="manage-column" scope="col">Operation</th>
				</tr>
			</thead>
			<form method="post" action="">
		<?php $mobile_types = websitez_get_mobile_types(); foreach($mobile_types as $type): ?>
			<?
			$option = get_option($type['option']);
			$redirect = get_option($type['url_redirect']);
			?>
			<form method="post" action="">
				<input type="hidden" name="action" value="<?= $type['option'] ?>">
			<tr valign="top" class="author-self status-publish iedit">
				<th scope="row"><?php _e($type['name']) ?></th>
				<td>
					<select name="<?= $type['option'] ?>" class="theme_template">
							<option name="theme_template" value="<?php echo WEBSITEZ_DEFAULT_THEME; ?>">Please select a mobile theme...</option>
							<?php foreach($current_themes_installed as $name => $mobile_theme): ?>
							<option name="theme_template" value="<?php echo $mobile_theme['Template']; ?>" <?php if($option==$mobile_theme['Template']) echo 'selected="selected"'; ?>><?php _e($name); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<?/*<td><input type="hidden" name="url_field" value="<?= $type['url_redirect'] ?>"><input type="text" name="redirect_url" value="<?=$redirect?>" size="40"></td>*/?>
				<td>
					<input type="submit" class="button submit" value="Update">
				</td>
			</tr>
		</form>
		<?php $i++; endforeach; ?>
		</table>
		<?
		$websitez_record_stats = get_option(WEBSITEZ_RECORD_STATS_NAME);
		$websitez_use_preinstalled_themes = get_option(WEBSITEZ_USE_PREINSTALLED_THEMES_NAME);
		?>
		<form action="" method="POST">
		<div style="margin:10px 0;">
			<table class="widefat post fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="manage-column" scope="col" width="445">Record mobile statistics?</th>
						<th class="manage-column" scope="col">Operation</th>
					</tr>
				</thead>
				<tr valign="top" class="author-self status-publish iedit">
					<td>
						<select name="record_stats" class="theme_template" style="width: 100px;">
								<option value="true" <? if($websitez_record_stats == "true") echo "selected";?>><?php _e('Yes'); ?></option>
								<option value="false" <? if($websitez_record_stats == "false") echo "selected";?>><?php _e('No'); ?></option>
						</select>
					</td>
					<td>
						<input type="submit" class="button submit" value="Update">
					</td>
				</tr>
			</table>
		</div>
		</form>
		<form action="" method="POST">
		<div style="margin:10px 0;">
			<table class="widefat post fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="manage-column" scope="col" width="445">Use pre-installed themes?</th>
						<th class="manage-column" scope="col">Operation</th>
					</tr>
				</thead>
				<tr valign="top" class="author-self status-publish iedit">
					<td>
						<select name="use_preinstalled_themes" class="theme_template" style="width: 100px;">
								<option value="true" <? if($websitez_use_preinstalled_themes == "true") echo "selected";?>><?php _e('Yes'); ?></option>
								<option value="false" <? if($websitez_use_preinstalled_themes == "false") echo "selected";?>><?php _e('No'); ?></option>
						</select>
					</td>
					<td>
						<input type="submit" class="button submit" value="Update">
					</td>
				</tr>
			</table>
		</div>
		</form>
		<div>
			<?
			//Get dynamic footer
			_e(websitez_dynamic_footer());
			?>
		</div>
</div>
<?php
}

/*
Get the dynamic footer remotely
*/
function websitez_dynamic_footer(){
	$websitez_footer = file_get_contents("http://websitez.com/api/websitez-wp-mobile-detector/footer.php");
	return $websitez_footer;
}

/*
Get dynamic offers for customers
*/
function websitez_dynamic_offers(){
	$websitez_offers = file_get_contents("http://websitez.com/api/websitez-wp-mobile-detector/offers.php");
	return $websitez_offers;
}

/*
Get dynamic offers for customers
*/
function websitez_dynamic_offers_stats(){
	$websitez_offers = file_get_contents("http://websitez.com/api/websitez-wp-mobile-detector/offers-stats.php");
	return $websitez_offers;
}
?>