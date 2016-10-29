<?php get_header(); ?>
		
		<div id="content" class="part">
			<?php
			if(strlen($websitez_options['ads']['show_header_snippet']) > 0){
				echo stripslashes($websitez_options['ads']['show_header_snippet']);
			}
			?>
			<?php if(have_posts()) : ?>
				<div id="posts_container">
				<?php while(have_posts()) : the_post(); ?>
					<?php $first_image = wz_boot_get_first_image(get_the_content(),get_the_ID()); ?>
					<a href="<?php the_permalink(); ?>">
						<div class="the_post<?php if(!$first_image) echo " no_image"; ?>" <?php if($first_image) echo 'style="background-image: url(\''.WEBSITEZ_PLUGIN_WEB_DIR.'/timthumb.php?src='.urlencode($first_image).'&w=400\'); background-position: center bottom; background-repeat: no-repeat; min-height: 200px;"'; ?>>
							<div class="content<?php if($first_image) echo " image"; ?>">
								<h2><?php the_title(); ?></h2>
								<?php if($websitez_options['general']['no_comments'] != 'yes'){ ?><div class="comments"><?php comments_number( '0', '1', '%' ); ?> <i class="icon-comment icon-white"></i></div><?php } ?>
								<div class="meta"><?php if($websitez_options['general']['no_authors'] != 'yes'){ ?><?php the_author(); ?> | <?php } ?><?php if($websitez_options['general']['no_creation'] != 'yes'){ ?><?php echo wz_calculate_time(get_the_date('Y-m-d H:i:s')); ?><?php } ?></div>
							</div>
						</div>
					</a>
				<?php endwhile; ?>
				</div>
				<div id="pagination">
					<div id="previous">
						<?php previous_posts_link('<i class="icon-arrow-left icon-white"></i> Newer Entries', 0) ?>
					</div>
					<div id="next">
						<?php next_posts_link('Older Entries <i class="icon-arrow-right icon-white"></i>', 0); ?>
					</div>
					<div style="clear: both;"></div>
				</div>
			<?php endif; ?>
			
			<?php
			if(strlen($websitez_options['ads']['show_footer_snippet']) > 0){
				echo stripslashes($websitez_options['ads']['show_footer_snippet']);
			}
			?>
		</div><!-- END CONTENT -->
		
		<?php get_sidebar('left_home'); ?>
		
		<?php get_sidebar('right_home'); ?>
		
<?php get_footer(); ?>