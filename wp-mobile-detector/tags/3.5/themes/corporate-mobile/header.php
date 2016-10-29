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
  var twitter_request = false;
  
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
	
	function show_twitter_feed(username){
		<?php
		if($websitez_options['general']['twitter_username_action'] == "twitter"){
		?>
		window.location = "http://www.twitter.com/"+username;
		<?php
		}else{
		?>
		var limit = 10;
		if(twitter_request != true){
			jQuery.get('<?php bloginfo('template_url'); ?>/twitter.php?twitter_username='+username, function(data) {
			  var items = JSON.parse(data);
			  var stuff = "<ul>";
			  for(i=0;i<items.length;i++){
			  	if(i==10)
			  		break;
			  	stuff += "<li><a href='http://twitter.com/"+username+"/statuses/"+items[i].id_str+"'>"+items[i].text+"</a></li>";
			  }
			  stuff += "</ul>";
			  jQuery("#twitter_feed").html(stuff);
			  jQuery("#twitter_feed").toggle("slow");
			  twitter_request = true;
			});
		}else{
			jQuery("#twitter_feed").toggle("slow");
		}
		<?php
		}
		?>
	}
	</script>
	<?php wp_head() ?>
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
		<a href="<?php bloginfo('url'); ?>" class="websitez-header-right"><div id="home-image"></div></a>
	</div>
	<?php
	if(is_home() && $websitez_options['sidebar']['show_pages'] == "yes"){
	?>
	<div id="menu" class="websitez-menu">
		<?php
		if(strlen($websitez_options['general']['facebook_url']) > 0 || strlen($websitez_options['general']['twitter_username']) > 0){
			echo "<div style='text-align: right; padding: 0px 5px 5px 0px;'>";
			if(strlen($websitez_options['general']['facebook_url']) > 0)
				echo "<a href='".$websitez_options['general']['facebook_url']."' target='_blank'><img src='".get_bloginfo('template_url')."/images/facebook-icon.png' border='0'></a> ";
			if(strlen($websitez_options['general']['twitter_username']) > 0)
				echo "<a href='' onClick='show_twitter_feed(\"".$websitez_options['general']['twitter_username']."\"); return false;'><img src='".get_bloginfo('template_url')."/images/twitter-icon.png' border='0'></a> ";
			echo "</div>";
		}
		$pages = websitez_get_ordered_pages($websitez_options['sidebar']['show_pages_items']);
		$i=0;
		foreach($pages as $page):
		?>
		<div id="websitez-page-<?php echo $i;?>" class="websitez-menu-pages" style="<?php if($i!=0) echo 'display: none;';?>">
		<a href="<?php echo get_page_link($page->ID) ?>"><div class="websitez-view">
			<h3><?php echo $page->post_title;?></h3>
			<p><?php echo websitez_home_page_content($page->post_content); ?></p>
		</div></a>
		</div>
		<?php
		$i++;
		endforeach;
		?>
	</div>
	<a href="" onClick="websitez_change_page('previous'); return false;"><div class="websitez-menu-previous websitez-menu-height"></div></a>
	<a href="" onClick="websitez_change_page('next'); return false;"><div class="websitez-menu-next websitez-menu-height"></div></a>
	<div class="websitez-menu-count websitez-menu-height"><div class="websitez-menu-count-content">
		<?php
		for($z=1;$z<=$i;$z++){
			if($z==1)
				echo "<a href='' id='websitez-page-links-".$z."' onclick='websitez_switch_page(\"".$z."\"); return false;' class='websitez-pages websitez-page-links-active'>".$z."</a>";
			else
				echo "<a href='' id='websitez-page-links-".$z."' onclick='websitez_switch_page(\"".$z."\"); return false;' class='websitez-pages websitez-page-links'>".$z."</a>";
		}
		?>
	</div></div>
	<script type="application/x-javascript">
	var websitez_page_num = 0;
	var websitez_total_pages = "<?php echo (count($pages)-1); ?>";
	var websitez_active_page = "websitez-page-0";
	
	function websitez_switch_page(page_number){
		var page_num = page_number-1;
		if(page_num != websitez_page_num){
			if(page_num > websitez_page_num){
				var number_of_pages = page_num - websitez_page_num;
				for(i=0;i<number_of_pages;i++){
					websitez_change_page('next');
				}
			}else if(page_num < websitez_page_num){
				var number_of_pages = websitez_page_num - page_num;
				for(i=0;i<number_of_pages;i++){
					websitez_change_page('previous');
				}
			}
		}
	}
	
	function websitez_change_page(direction){
		if(direction == "previous"){
			if(websitez_page_num == 0)
				websitez_page_num = websitez_total_pages;
			else
				websitez_page_num--;
		}else if(direction == "next"){
			if(websitez_page_num == websitez_total_pages)
				websitez_page_num = 0;
			else
				websitez_page_num++;
		}
		
		jQuery('.websitez-pages').each(function(){jQuery(this).removeClass('websitez-page-links-active').addClass('websitez-page-links')});
		jQuery('#websitez-page-links-'+(websitez_page_num+1)).addClass('websitez-page-links-active');
		
		if(direction == "next"){
			jQuery("#"+websitez_active_page).animate({
		    opacity: 0,
		    marginLeft: '-200px'
		  }, {
		    duration: 300,
		    complete: function() {
		      jQuery(this).hide();
					jQuery("#websitez-page-"+websitez_page_num).attr("style", "opacity: 0; margin-left: 200px;");
					jQuery("#websitez-page-"+websitez_page_num).show();
		      jQuery("#websitez-page-"+websitez_page_num).animate({
				    opacity: 1,
				    marginLeft: '0px'
				  }, {
				    duration: 300,
				    complete: function() {
				      jQuery(this).show();	
				    }
				  });
		    }
		  });
	  }else if(direction == "previous"){
	  	jQuery("#"+websitez_active_page).animate({
		    opacity: 0,
		    marginLeft: '+200px'
		  }, {
		    duration: 300,
		    complete: function() {
		      jQuery(this).hide();
					jQuery("#websitez-page-"+websitez_page_num).attr("style", "opacity: 0; margin-left: -200px;");
					jQuery("#websitez-page-"+websitez_page_num).show();
		      jQuery("#websitez-page-"+websitez_page_num).animate({
				    opacity: 1,
				    marginLeft: '0px'
				  }, {
				    duration: 300,
				    complete: function() {
				      jQuery(this).show();	
				    }
				  });
		    }
		  });
	  }
	  
	  //jQuery("#websitez-page-"+websitez_page_num).show("slow");

		websitez_active_page = "websitez-page-"+websitez_page_num;
	}
	</script>
	<?php
	}
	?>
	<div id="twitter_feed" style="display: none;"></div>
	<?php
	if($websitez_options['sidebar']['show_search'] == "yes"){
		echo websitez_get_search_form();
	}
	?>