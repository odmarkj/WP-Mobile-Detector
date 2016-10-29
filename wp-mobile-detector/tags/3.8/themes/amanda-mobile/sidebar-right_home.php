<?php 
$websitez_options = websitez_get_options();
$menu_order = explode(",",$websitez_options['sidebar']['menu_order']);
?>
<div id="rbMenu-click"></div>
<div id="rbMenu" style="display: none;">
	<div class="element home">
		<a href="<?php bloginfo('home'); ?>"><i class="icon-arrow-left icon-white"></i> <?php _e('Go to homepage'); ?></a>
	</div>
	<?php foreach($menu_order as $menu): ?>
	<?php if($menu == "show_pages" && $websitez_options['sidebar']['show_pages'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-book"></i> <?php _e('Pages'); ?></h3>
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
		<h3><i class="icon-user"></i> <?php _e('Meta'); ?></h3>
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
	<?php if(function_exists('wp_nav_menu')){ ?>
	<?php $custom_menus = explode(",", $websitez_options['sidebar']['custom_menu_ids']); ?>
	<?php if(count($custom_menus) > 0 && strlen($custom_menus[0]) > 0){ ?>
	<?php $nav_menus = wp_get_nav_menus(); ?>
	<?php foreach($custom_menus as $cm){ ?>
	<?php if(strlen($cm) > 0){ ?>
	<?php $menu = wp_nav_menu( array('container'=>false,'echo'=>false, 'menu' => $cm) ); ?>
	<?php if(strlen($menu) > 0){ ?>
	<?php $name = "Menu"; ?>
	<?php foreach($nav_menus as $nm){ ?>
		<?php if($nm->term_id == $cm){ $name = $nm->name; } ?>
	<?php } ?>
		<div class="element">
			<h3><i class="icon-user"></i> <?php echo $name; ?></h3>
			<?php echo $menu; ?>
		</div>
	<?php } ?>
	<?php } ?>
	<?php } ?>
	<?php }else{ ?>
	<?php if(strlen($websitez_options['sidebar']['custom_nav_menu_id']) > 0){ ?>
	<?php $menu = wp_nav_menu( array('container'=>false,'echo'=>false, 'menu' => $websitez_options['sidebar']['custom_nav_menu_id']) ); ?>
	<?php if(strlen($menu) > 0): ?>
		<div class="element">
			<h3><i class="icon-user"></i> <?php _e('Menu'); ?></h3>
			<?php echo $menu; ?>
		</div>
	<?php endif; ?>
	<?php } ?>
	<?php } ?>
	<?php } ?>
	<?php elseif($menu == "show_search" && $websitez_options['sidebar']['show_search'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-search"></i> <?php _e('Search'); ?></h3>
		<form action="<?php echo get_option('home'); ?>" method="GET">
			<input type="text" name="s" placeholder="<?php _e('Enter a keyword...'); ?>"> &nbsp;<input type="submit" value="<?php _e('Go!'); ?>" class="btn">
		</form>
	</div>
	<?php elseif($menu == "show_categories" && $websitez_options['sidebar']['show_categories'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-folder-open"></i> <?php _e('Categories'); ?></h3>
		<ul>
		<?php
		if(strlen($websitez_options['sidebar']['show_categories_items']) > 2){
			$args = array(
				'include' => $websitez_options['sidebar']['show_categories_items'],
				'hide_empty' => 0
			);
			$category_ids = explode(",", $websitez_options['sidebar']['show_categories_items']);
			$categories = get_categories( $args );
			foreach($category_ids as $cid){
				foreach($categories as $category){
					if($cid == $category->cat_ID){
						echo "<li class='cat_item'><a href='".get_the_permalink($category->cat_ID)."'>".$category->name."</a></li>\n";
					}
				}
			}
		}else{
			wp_list_pages('title_li=');
		}
		?>
		<?php //wp_list_categories('show_count=0&title_li=&depth=1'); ?>
		</ul>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>
	<div class="element">
		<?php wp_footer(); ?>
	</div>
</div><!-- END RBMENU -->