	<?php
	$websitez_options = get_websitez_options();
	if(strlen($websitez_options['general']['facebook_url']) > 0 || strlen($websitez_options['general']['twitter_username']) > 0){
		echo "<div class='websitez-footer' style='text-align: center; padding: 0px;'>";
		if(strlen($websitez_options['general']['facebook_url']) > 0)
			echo "<a href='".$websitez_options['general']['facebook_url']."' target='_blank'><img src='".get_bloginfo('template_url')."/images/facebook-icon.png' border='0'></a> ";
		if(strlen($websitez_options['general']['twitter_username']) > 0)
			echo "<a href='http://twitter.com/".$websitez_options['general']['twitter_username']."'><img src='".get_bloginfo('template_url')."/images/twitter-icon.png' border='0'></a> ";
		echo "</div>";
	}
	?>
	<?php wp_footer(); ?>
	<a name="bottom"></a>
	<?php
	if(strlen($websitez_options['ads']['show_footer_snippet']) > 0){
		echo stripslashes($websitez_options['ads']['show_footer_snippet']);
	}
	?>
</body>
</html>