<?php 
$websitez_options = websitez_get_options();
$menu_order = explode(",",$websitez_options['sidebar']['menu_order']);
?>
<div id="rbMenu" style="display: none;">
	<?php foreach($menu_order as $menu): ?>
	<?php if($menu == "show_search" && $websitez_options['sidebar']['show_search'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-search icon-white"></i> <?php _e('Search'); ?></h3>
		<form action="<?php echo get_option('home'); ?>" method="GET">
			<input type="text" name="s" placeholder="<?php _e('Enter a keyword...'); ?>"> &nbsp;<input type="submit" value="<?php _e('Go!'); ?>" class="btn">
		</form>
	</div>
	<?php elseif($menu == "show_categories" && $websitez_options['sidebar']['show_categories'] == "yes"): ?>
	<div class="element">
		<h3><i class="icon-folder-open icon-white"></i> <?php _e('Categories'); ?></h3>
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
		</ul>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>
</div><!-- END RBMENU -->