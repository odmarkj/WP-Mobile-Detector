<?php $websitez_options = websitez_get_options(); ?>
<div id="loading" style="display: none;">
	Loading<span id="loading-flash">...</span>
</div>
<script type="text/javascript">
	setInterval(function(){ var d = document.getElementById('loading-flash'); if(d.innerHTML.length == 0){ d.innerHTML = "..."; }else{ d.innerHTML = ""; } }, 500);
</script>
<div id="contain_all">
	<div id="header" class="ghost_bar">
		<div class="rMenu">
			<div class="bar">
				<i class="icon-th-large icon-white"></i>
	  		</div>
		</div>
		<div class="brand">
			<?php if(strlen($websitez_options['images']['logo']) > 0): ?>
				<img src='<?php echo $websitez_options['images']['logo'];?>' border='0' alt=''>
			<?php else: ?>
				<?php bloginfo('name');?>
			<?php endif; ?>
		</div>
	</div><!-- END HEADER -->