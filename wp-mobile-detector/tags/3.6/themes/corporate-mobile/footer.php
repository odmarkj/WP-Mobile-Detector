	<?php wp_footer(); ?>
	<a name="bottom"></a>
	<?php
	if(strlen($websitez_options['ads']['show_footer_snippet']) > 0){
		echo stripslashes($websitez_options['ads']['show_footer_snippet']);
	}
	?>
</body>
</html>