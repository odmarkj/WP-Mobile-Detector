<?php
$websitez_options = websitez_get_options();
$menu_order = explode(",",$websitez_options['sidebar']['menu_order']);

foreach($menu_order as $menu):
	if($menu == "show_search_div" && $websitez_options['sidebar']['show_search'] == "yes"){
		get_search_form();
	}else if($menu == "show_menu_div" && $websitez_options['sidebar']['show_menu'] == "yes"){
		$menu = wp_nav_menu( array('container'=>false,'echo'=>false) );
		if(strlen($menu) > 0){
			echo "<div class='websitez-sidebar'>";
			echo "<h3>Menu</h3>";
			echo $menu;
			echo "</div>";
		}
	}else if($menu == "show_pages_div" && $websitez_options['sidebar']['show_pages'] == "yes"){
		?>
		<div class="websitez-sidebar">
			<h3>Pages</h3>
			<ul>
			<?php
			if($websitez_options['sidebar']['show_pages_items'] != "")
				wp_list_pages('title_li=&include='.$websitez_options['sidebar']['show_pages_items']);
			else
				wp_list_pages('title_li=');
			?>
			</ul>
		</div>
		<?php
	}else if($menu == "show_categories_div" && $websitez_options['sidebar']['show_categories'] == "yes"){
		?>
		<div class="websitez-sidebar">
			<h3>Categories</h3>
			<ul>
		  <?php
			if($websitez_options['sidebar']['show_categories_items'] != "")
				wp_list_categories('show_count=1&title_li=&include='.$websitez_options['sidebar']['show_categories_items']);
			else
				wp_list_categories('show_count=1&title_li=');
			?>
			</ul>
		</div>
		<?php
	}else if($menu == "show_blogroll_div" && $websitez_options['sidebar']['show_blogroll'] == "yes"){
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
	}else if($menu == "show_meta_div" && $websitez_options['sidebar']['show_meta'] == "yes"){
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