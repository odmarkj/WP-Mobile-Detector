<?php
if ( ! isset( $content_width ) )
	$content_width = 480;

do_action( 'websitez_functions_start' );
add_action( 'after_setup_theme', 'set_websitez_options' );
add_action( 'after_setup_theme', 'websitez_setup' );

function get_websitez_options(){
	return $GLOBALS['websitez_options'];
}

function set_websitez_options(){
	$GLOBALS['websitez_options'] = websitez_get_options();
}

function websitez_insert_logo(){
	if (is_feed()){
		return;
	}

	ob_start("websitez_insert_logo_now");
}

function websitez_insert_logo_now($html){
	$options = websitez_get_options();
	if($options && strlen($options['images']['logo']) > 0){
		$html = preg_replace("/<a.*class=\"logo\">(.*?)<\/a>/im", "<a href='".get_bloginfo('url')."' class='logo'><img src='".$options['images']['logo']."' border='0' alt=''></a><a href='".bloginfo('url')."' class='logo_addition'>".get_bloginfo('name')."</a>", $html);
	}
	return $html;
}

/*
Dynamically load stylesheet
*/
add_action( 'wp_print_styles', 'websitez_stylesheet');
if (!function_exists('websitez_stylesheet')){
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