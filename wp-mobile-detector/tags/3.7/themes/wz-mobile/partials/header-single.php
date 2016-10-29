<?php $websitez_options = websitez_get_options(); ?>
<div id="header" class="ghost_bar">
	<div class="lMenu">
		<a href="#" onclick="window.history.go(-1); return false;">
      <div class="bar">
        <i class="icon-arrow-left icon-white"></i>
      </div>
  	</a>
	</div>
	<div class="rMenu">
		<a href="#" onclick="wz_mobile.open_right_menu(); return false;">
      <div class="bar">
        <i class="icon-share icon-white"></i>
      </div>
  	</a>
	</div>
	<div class="brand">
		<?php if(strlen($websitez_options['images']['logo']) > 0): ?>
		<a href="<?php bloginfo('url'); ?>" class="logo"><img src='<?php echo $websitez_options['images']['logo'];?>' border='0' alt=''></a>
		<?php else: ?>
		<a href="<?php echo get_option('home'); ?>"><?php bloginfo('name');?></a>
		<?php endif; ?>
	</div>
</div><!-- END HEADER -->