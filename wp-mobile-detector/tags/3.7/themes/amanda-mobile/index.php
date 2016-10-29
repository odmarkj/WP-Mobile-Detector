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
					<div class="the_post">
						<?php
						$images = wz_boot_get_all_images(get_the_content(),get_the_ID());
						$i = 0;
						if(count($images) > 0){ ?>
						<div class="slider">
							<a href="#" style="float: left;" class="left"></a>
							<a href="#" style="float: right;" class="right"></a>
							<?php foreach($images as $image){ websitez_preload_image(WEBSITEZ_PLUGIN_WEB_DIR.'timthumb.php?src='.urlencode($image).'&w=360'); ?>
							<div class="slide" <?php echo 'style="background-image: url(\''.WEBSITEZ_PLUGIN_WEB_DIR.'timthumb.php?src='.urlencode($image).'&w=360\'); background-position: center bottom; background-repeat: no-repeat; min-height: 150px; max-height: 150px;'.($i != 0 ? ' display: none;' : '').'"'; ?>></div>
							<?php $i++; } ?>
						</div>
						<?php } ?>
						<div class="content" data-link="<?php the_permalink(); ?>">
							<?php
							$my_excerpt = get_the_excerpt();
							if ( '' != $my_excerpt ) {
							?>
							<h2><?php the_title(); ?></h2>
							<?php
							}else{
							?>
							<h2><?php the_title(); ?></h2>
							<?php
							}
							?>
							<?php if($websitez_options['general']['no_comments'] != 'yes'){ ?><div class="comments"><?php comments_number( '0', '1', '%' ); ?> <i class="icon-comment"></i></div><?php } ?>
							<div class="meta"><?php if($websitez_options['general']['no_authors'] != 'yes'){ ?><?php the_author(); ?> | <?php } ?><?php if($websitez_options['general']['no_creation'] != 'yes'){ ?><?php echo wz_calculate_time(get_the_date('Y-m-d H:i:s')); ?><?php } ?></div>
							<?php if ( '' != $my_excerpt ) { ?>
							<div class="excerpt" style="display: none;">
								<?php echo $my_excerpt; ?>
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
									<div class="more">Read More <i class="icon-arrow-right"></i></div>
								</a>
							</div>
							<?php } ?>
						</div>
					</div>
				<?php endwhile; ?>
				</div>
				<div id="pagination">
					<div id="previous">
						<?php previous_posts_link('<i class="icon-arrow-left"></i> Newer Entries') ?>
					</div>
					<div id="next">
						<?php next_posts_link('Older Entries <i class="icon-arrow-right"></i>'); ?>
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
		
		<?php get_sidebar('right_home'); ?>
		
<?php get_footer(); ?>