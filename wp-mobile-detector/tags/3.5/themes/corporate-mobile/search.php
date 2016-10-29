<?php get_header(); ?>
<div class="websitez-container">
	<h4>Search results for '<?php echo htmlentities($_GET['s']);?>'</h4>
	<?php if(have_posts()) : ?>
		<?php $i=0; ?>
		<?php while(have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post-wrapper">
					<?php if($websitez_options['general']['no_creation'] != 'yes'){ ?>
					<div class="calendar">
		    		<div class="calendar-month">
		    			<?php the_time('M') ?>
		    		</div>
		    		<div class="calendar-day">
		    			<?php the_time('j') ?>
		    		</div>
		    	</div>
		    	<?php } ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="post-title"><?php the_title(); ?></a>
					<?php if($websitez_options['general']['no_authors'] != 'yes'){ ?>
					<p class="post-author"><?php echo __("Author: <strong>".the_author()); ?></strong></p>
					<?php } ?>
					<div class="post-entry<?php if($i!=0) echo " hidden";?> eid<?php echo $i;?>"><?php the_excerpt(); ?></div>
					<div style="clear: both;"></div>
				</div>
				<div class="post-more">
					<a href="<?php the_permalink(); ?>"><p class="post-read-more<?php if($i!=0) echo " hidden";?> rid<?php echo $i;?>"><?php echo __("Read This Post"); ?></p></a>
					<a href="" onClick="jQuery('.eid<?php echo $i;?>').toggle('slow'); jQuery('.rid<?php echo $i;?>').toggle('slow'); jQuery('.vid<?php echo $i;?>').toggle(); return false;"><p class="post-view-more<?php if($i!=0) echo " hidden";?> vid<?php echo $i;?>"><img src="<?php bloginfo('template_url'); ?>/images/small-up-arrow.png" border="0"></p></a>
					<a href="" onClick="jQuery('.eid<?php echo $i;?>').toggle('slow'); jQuery('.rid<?php echo $i;?>').toggle('slow'); jQuery('.vid<?php echo $i;?>').toggle(); return false;"><p class="post-view-more<?php if($i==0) echo " hidden";?> vid<?php echo $i;?>"><img src="<?php bloginfo('template_url'); ?>/images/small-down-arrow.png" border="0"></p></a>
					<div style="clear: both;"></div>
				</div>
			</div>
			<?php $i++; ?>
		<?php endwhile; ?>
	  <div class="websitez-navigation">
	  	<?php posts_nav_link(' &#124; ','&#171; previous','next &#187;'); ?>
	  </div>          
	<?php else : ?>
		<div class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-wrapper">
				<h3><?php _e('No posts are added.'); ?></h3>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php get_footer(); ?>      