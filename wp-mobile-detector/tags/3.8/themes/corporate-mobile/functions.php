<?php
if ( ! isset( $content_width ) )
	$content_width = 480;

add_action( 'after_setup_theme', 'set_websitez_options' );
add_action( 'after_setup_theme', 'websitez_setup' );

function websitez_get_search_form(){

return '<form role="search" method="get" id="searchform" action="<?php echo home_url( \'/\' ); ?>">
	<div class="websitez-search">
    <input type="text" value="" name="s" id="s" class="websitez-search-input"/>
  </div>
</form><div style="clear: both;"></div>';
}


function get_websitez_options(){
	return $GLOBALS['websitez_options'];
}

function set_websitez_options(){
	$GLOBALS['websitez_options'] = websitez_get_options();
}

function websitez_get_ordered_pages($ordered_pages){
	$final_pages = array();
	$desired_pages = explode(",",$ordered_pages);
	$pages = get_pages('include='.$ordered_pages);
	if($ordered_pages != ""){
		foreach($desired_pages as $dpage):
			foreach($pages as $page):
				if($dpage == $page->ID)
					$final_pages[] = $page;
			endforeach;
		endforeach;
		
		return $final_pages;
	}else{
		return $pages;
	}
}

function websitez_home_page_content($content){
	$content = strip_tags($content);
	if(strlen($content) > 200){
		$content = substr($content,0,200);
		$content .= "...";
	}
	
	return $content;
}

/*
Dynamically load stylesheet
*/
add_action( 'wp_print_styles', 'websitez_stylesheet');
if (!function_exists('websitez_setup')){
	function websitez_stylesheet() {
		$myStyleUrl = get_template_directory_uri().'/style.php?path='.urlencode(get_site_url(null, '/api/websitez-options.json'));
		wp_register_style('websitez-custom-stylesheet', $myStyleUrl);
		wp_enqueue_style( 'websitez-custom-stylesheet');
	}
}

if (!function_exists('websitez_setup')){
	function websitez_setup() {
		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );
	}
}

function websitez_comment($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;
?>
	<div class="websitez-comments" <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<div class="websitez-comments-author">
			<p class="websitez-comments-gravatar"><?php  if(function_exists('get_avatar')){ echo get_avatar($comment, '80'); } ?></p>
			<p class="websitez-comments-author-link"><?php  comment_author_link() ?></p>
		</div>
		<?php if ($comment->comment_approved == '0') : ?>
	   	<p class="websitez-comments-awaiting-moderation"><?php _e('This comment is awaiting moderation.'); ?></p>
	  <?php endif; ?>
		<p class="websitez-comments-text"><?php comment_text() ?></p>
		<p class="websitez-comments-reply"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></p>
		<div style="clear: both;"></div>
	</div>
<?php
}
?>