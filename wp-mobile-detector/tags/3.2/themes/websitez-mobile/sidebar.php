<?php
$websitez_options = websitez_get_options();
$menu_order = explode(",",$websitez_options['sidebar']['menu_order']);

foreach($menu_order as $menu):
	if($menu == "show_search" && $websitez_options['sidebar']['show_search'] == "yes"){
		get_search_form();
	}else if($menu == "show_menu" && $websitez_options['sidebar']['show_menu'] == "yes"){
		if(function_exists('wp_nav_menu')){
			$args = array('container'=>false,'echo'=>'0');
			if(strlen($websitez_options['sidebar']['custom_nav_menu_id']) > 0){
				$args['menu'] = $websitez_options['sidebar']['custom_nav_menu_id'];
			}
			$menu = wp_nav_menu( $args );
			if(strlen($menu) > 0){
				echo "<div class='websitez-sidebar'>";
				echo "<h3>Menu</h3>";
				echo $menu;
				echo "</div>";
			}
		}
	}else if($menu == "show_pages" && $websitez_options['sidebar']['show_pages'] == "yes"){
		?>
		<div class="websitez-sidebar">
			<h3>Pages</h3>
			<ul>
			<?php
			if(strlen($websitez_options['sidebar']['show_pages_items']) > 0){
				$pages_in_order = explode(",", $websitez_options['sidebar']['show_pages_items']);
				$pages = get_pages(array(
					'include' => $websitez_options['sidebar']['show_pages_items'],
					'post_status' => 'publish'
				));
				foreach($pages_in_order as $pio){
					foreach($pages as $page){
						if($pio == $page->ID){
							echo "<li><a href='".get_page_link($page->ID)."'>".$page->post_title."</a></li>";
							break;
						}
					}
				}
			}else{
				wp_list_pages('title_li=');
			}
			?>
			</ul>
		</div>
		<?php
	}else if($menu == "show_categories" && $websitez_options['sidebar']['show_categories'] == "yes"){
		?>
		<div class="websitez-sidebar">
			<h3>Categories</h3>
			<?php //echo $websitez_options['sidebar']['show_categories_items']; ?>
			<ul>
		  <?php
			if(strlen($websitez_options['sidebar']['show_categories_items']) > 0){
				$categories_in_order = explode(",", $websitez_options['sidebar']['show_categories_items']);
				$categories = get_categories(array(
					'include' => $websitez_options['sidebar']['show_categories_items'],
					'hide_empty' => '0',
					'parent' => 0
				));
				foreach($categories_in_order as $cio){
					foreach($categories as $category){
						if($cio == $category->cat_ID){
							echo "<li><a href='".get_category_link($category->cat_ID)."'>".$category->cat_name."</a></li>";
							break;
						}
					}
				}
			}else{
				wp_list_categories('show_count=1&title_li=&hide_empty=0');
			}
			?>
			</ul>
		</div>
		<?php
	}else if($menu == "show_blogroll" && $websitez_options['sidebar']['show_blogroll'] == "yes"){
		?>
		<div class="websitez-sidebar">
			<h3>Blogroll</h3>
			<ul>
		  <?php
			if($websitez_options['sidebar']['show_blogroll_items'] != "")
				wp_list_bookmarks(array('title_li'=>'','categorize'=>0,'include'=>$websitez_options['sidebar']['show_blogroll_items']));
			else
				wp_list_bookmarks(array('title_li'=>'','categorize'=>0));
			?>
			</ul>
		</div>
		<?php
	}else if($menu == "show_meta" && $websitez_options['sidebar']['show_meta'] == "yes"){
		?>
		<div class="websitez-sidebar">
			<h3>Meta</h3>
			<ul>
			    <?php
			    if(is_user_logged_in()){
			    	$register = wp_register('','',false);
			    	if(strlen($register) > 0)
			    		echo "<li><a href='/wp-admin/' rel='external'>Site Admin</a></li>\n";
			    	echo "<li><a href='".wp_logout_url()."' rel='external'>Logout</a></li>\n";
			    }else{
			    	$register = wp_register('','',false);
			    	if(strlen($register) > 0)
			    		echo "<li><a href='/wp-login.php?action=register' rel='external'>Register</a></li>\n";
			    	echo "<li><a href='".wp_login_url()."' rel='external'>Login</a></li>\n";
			    }
			    ?>
			    <?php wp_meta(); ?>
			</ul>
		</div>
		<?PHP
	}
endforeach;
?>