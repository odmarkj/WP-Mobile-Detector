<?php get_header(); ?>
<div class="websitez-container">
	<?php if(have_posts()) : ?>
		<?php $i=0; ?>
		<?php while(have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post-wrapper" style="padding-bottom: 10px;">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="post-title"><?php the_title(); ?></a>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post-wrapper">
					<div class="post-entry"><?php the_content(); ?></div>
					<?php wp_link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
					<div style="clear: both;"></div>
				</div>
			</div>
			<?php if($websitez_options['general']['no_comments'] != 'yes'){ ?>
			<div class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post-wrapper">
					<?php comments_template(); ?>
				</div>
			</div>
			<?php } ?>
			<?php $i++; ?>
		<?php endwhile; ?>
	  <div class="navigation">
	  	<?php posts_nav_link(' &#124; ','&#171; previous','next &#187;'); ?>
	  </div>          
	<?php else : ?>
		<div class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2><?php _e('No posts are added.'); ?></h2>
		</div>
	<?php endif; ?>
</div>
<?php get_footer(); ?>