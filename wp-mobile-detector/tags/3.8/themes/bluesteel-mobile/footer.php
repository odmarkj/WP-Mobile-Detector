	<div class="websitez-footer">
		<?php wp_footer(); ?>
	</div>
	<?php
	$websitez_options = websitez_get_options();
	if(strlen($websitez_options['ads']['show_footer_snippet']) > 0){
		echo stripslashes($websitez_options['ads']['show_footer_snippet']);
	}
	?>
</body>
</html>