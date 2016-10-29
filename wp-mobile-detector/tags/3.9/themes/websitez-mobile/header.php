<?php
$websitez_options = get_websitez_options();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php wp_title(); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
  <meta name="description" content="<?php bloginfo('description'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen" />
	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wp_enqueue_script( 'jquery' ); ?>
	<?php wp_head() ?>
	<script type="application/x-javascript">
  addEventListener("load", function(){
      setTimeout(updateLayout, 0);
  }, false);

  var currentWidth = 0;
  
  function updateLayout(){
    if (window.innerWidth != currentWidth){
      currentWidth = window.innerWidth;

      var orient = currentWidth == 320 ? "profile" : "landscape";
      document.body.setAttribute("orient", orient);
      setTimeout(function(){
      	window.scrollTo(0, 1);
      }, 100);            
    }
  }
  
  jQuery(document).ready(function() {
		setInterval(updateLayout, 400);
	});
	</script>
</head>
<body <?php body_class(); ?>>
<?php
if(strlen($websitez_options['ads']['show_header_snippet']) > 0){
	echo stripslashes($websitez_options['ads']['show_header_snippet']);
}
?>
	<a name="top"></a>
	<div id="header" class="websitez-header">
		<?php
		if("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != get_bloginfo('url')."/"){
		?>
		<a href="<?php bloginfo('url'); ?>"><div id="home-image" class="websitez-header-left"></div></a>
		<?php
		}
		if(strlen($websitez_options['images']['logo']) > 0){
		?>
		<a href="<?php bloginfo('url'); ?>" class="logo"><img src='<?php echo $websitez_options['images']['logo'];?>' border='0' alt=''></a>
		<?php
		}else{
		?>
		<a href="<?php bloginfo('url'); ?>" class="logo"><?php bloginfo('name'); ?></a>
		<?php
		}
		?>
		<a href="#" onClick="websitez_extendMenu(); return false;" class="websitez-header-right"></a>
	</div>
	<div id="menu" class="websitez-menu">
		<div id="sidebar" class="websitez-menu-content">
			<?php get_sidebar(); ?>
			<div style="clear: both;"></div>
		</div>
		<a id="menu-open" onClick="jQuery('.websitez-menu-content').toggle('slow'); jQuery('.hid').toggle(); return false;" href="#"><div class="websitez-menu-button hid"><img src="<?php bloginfo('template_url'); ?>/images/small-down-arrow.png" border="0"></div></a>
		<a id="menu-close" onClick="jQuery('.websitez-menu-content').toggle('slow'); jQuery('.hid').toggle();" href="#top"><div class="websitez-menu-button hid hidden"><img src="<?php bloginfo('template_url'); ?>/images/small-up-arrow.png" border="0"></div></a>
	</div>