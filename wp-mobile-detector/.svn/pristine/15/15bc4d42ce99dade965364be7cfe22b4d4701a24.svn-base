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
						<?php if(has_post_thumbnail()){ ?>
						<?php
						$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );
						$url = $thumb['0'];
						?>
						<div <?php echo 'style="background-image: url(\''.WEBSITEZ_PLUGIN_WEB_DIR.'/timthumb.php?src='.urlencode($url).'&w=360\'); background-position: center bottom; background-repeat: no-repeat; min-height: 150px; max-height: 150px;"'; ?>></div>
						<?php } ?>
						<div class="content">
							<h1><?php the_title(); ?></h1>
							<p>
								<?php the_content(); ?>
								<?php wp_link_pages(); ?>
							</p>
						</div>
					<?php endwhile; ?>
					<?php if($websitez_options['general']['no_comments'] != 'yes'){ ?>
					<div id="comments" class="content">
						<?php comments_template(); ?>
					</div>
					<?php } ?>
				</div>
			<?php else : ?>
			
			<?php endif; ?>
			
			<?php
			if(strlen($websitez_options['ads']['show_footer_snippet']) > 0){
				echo stripslashes($websitez_options['ads']['show_footer_snippet']);
			}
			?>

		</div><!-- END CONTENT -->
		
		<?php get_sidebar('right_home'); ?>
		
<?php get_footer(); ?>