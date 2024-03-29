<?php 
$websitez_options = websitez_get_options();
$menu_order = explode(",",$websitez_options['sidebar']['menu_order']);
?>
<div id="lbMenu" style="display: none;">
	<?php foreach($menu_order as $menu): ?>
	<?php if($menu == "show_pages" && $websitez_options['sidebar']['show_pages'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-book icon-white"></i> <?php _e('Pages'); ?></h3>
		<ul>
		<?php
		if(strlen($websitez_options['sidebar']['show_pages_items']) > 2){
			$args = array(
				'include' => $websitez_options['sidebar']['show_pages_items']
			);
			$page_ids = explode(",", $websitez_options['sidebar']['show_pages_items']);
			$pages = get_pages( $args );
			foreach($page_ids as $pid){
				foreach($pages as $page){
					if($pid == $page->ID){
						echo "<li class='page_item'><a href='".get_the_permalink($page->ID)."'>".$page->post_title."</a></li>\n";
					}
				}
			}
		}else{
			wp_list_pages('title_li=');
		}
		?>
		</ul>
	</div>
	<?php elseif($menu == "show_meta" && $websitez_options['sidebar']['show_meta'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-user icon-white"></i> <?php _e('Meta'); ?></h3>
		<ul>
	    <?php
	    if(is_user_logged_in()){
	    	$register = wp_register('','',false);
	    	if(strlen($register) > 0)
	    		echo "<li><a href='/wp-admin/' rel='external'>".__('Site Admin')."</a></li>\n";
	    	echo "<li><a href='".wp_logout_url()."' rel='external'>".__('Logout')."</a></li>\n";
	    }else{
	    	$register = wp_register('','',false);
	    	if(strlen($register) > 0)
	    		echo "<li><a href='/wp-login.php?action=register' rel='external'>".__('Register')."</a></li>\n";
	    	echo "<li><a href='".wp_login_url("/")."' rel='external'>".__('Login')."</a></li>\n";
	    }
	    ?>
	    <?php wp_meta(); ?>
		</ul>
	</div>
	<?php elseif($menu == "show_menu" && $websitez_options['sidebar']['show_menu'] == "yes" && function_exists('wp_nav_menu')): ?>
	<?php $menu = wp_nav_menu( array('container'=>false,'echo'=>false) ); ?>
	<?php if(strlen($menu) > 0): ?>
		<div class="element">
			<h3><i class="icon-user icon-white"></i> <?php _e('Menu'); ?></h3>
			<?php echo $menu; ?>
		</div>
	<?php endif; ?>
	<?php endif; ?>
	<?php endforeach; ?>
	<div class="element">
		<?php wp_footer(); ?>
	</div>
</div><!-- END LBMENU -->