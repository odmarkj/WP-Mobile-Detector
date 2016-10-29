<?php get_header(); ?>
		
		<div id="content" class="part">
			<?php
			if(strlen($websitez_options['ads']['show_header_snippet']) > 0){
				echo stripslashes($websitez_options['ads']['show_header_snippet']);
			}
			?>
			<?php if(have_posts()) : ?>
				<div id="individual_post">
					<?php while(have_posts()) : the_post(); ?>
						<?php $post_id = get_the_ID(); ?>
						<?php $images = wz_boot_get_all_images(get_the_content(),get_the_ID()); ?>
						<h1><?php the_title(); ?></h1>
						<p><?php the_content(); ?></p>
					<?php endwhile; ?>
					<?php if($websitez_options['general']['no_comments'] != 'yes'){ ?>
					<div id="comments">
						<?php comments_template(); ?>
					</div>
					<?php } ?>
				</div>
			<?php endif; ?>
			
			<?php
			if(strlen($websitez_options['ads']['show_footer_snippet']) > 0){
				echo stripslashes($websitez_options['ads']['show_footer_snippet']);
			}
			?>
		</div><!-- END CONTENT -->
		
		<?php get_sidebar('right_single'); ?>
		
<?php get_footer(); ?>