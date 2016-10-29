<?php $websitez_options = websitez_get_options(); ?>
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
							<p class="meta">
								<?php if($websitez_options['general']['no_authors'] != 'yes'){ ?>Posted by <?php the_author_posts_link(); ?> <?php } ?><?php if($websitez_options['general']['no_creation'] != 'yes'){ ?><?php echo wz_calculate_time(get_the_date('Y-m-d H:i:s')); ?><br><?php } ?>
								<?php if($websitez_options['general']['no_categories'] != 'yes'){ ?><?php _e("Category:", "wz-mobile"); ?> <?php the_category(', '); ?><br><?php } ?>
								<?php if($websitez_options['general']['no_tags'] != 'yes'){ ?><?php the_tags(__("Tags","wz-mobile") . ': ', ', ', ''); ?><?php } ?>
							</p>
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
			
			<?php $args = array( 'post_type' => 'post', 'posts_per_page' => 10, 'paged' => 1, 'post__not_in' =>array($post_id) ); ?>
			<?php $feed = new WP_Query( $args ); ?>
			<?php if($feed->have_posts()): ?>
			<div id="article_feed" style="background-color: #444; height: 100px; overflow: scroll; border-top: 1px solid #333;">
				<div style="width: <?php echo ($feed->post_count*100);?>px">
					<?php while ( $feed->have_posts() ) : $feed->the_post(); ?>
					<?php $first_image = wz_boot_get_first_image(get_the_content(),get_the_ID()); ?>
					<a href="<?php the_permalink(); ?>">
					<div style="float: left; height: 100px; width: 100px;">
						<div style="overflow: hidden; position: relative; height: 100px; border-right: 1px solid #333; border-left: 1px solid #555; background-position: center center; background-repeat: no-repeat;<?php if($first_image) echo " background-image: url('".$first_image."');"; ?>">
							<div style="padding: 5px; position: absolute; display: inline-block; right: 0px; left: 0px; bottom: 0px; font-size: 12px; text-shadow: black 1px 1px 1px; color: #fff; font-weight: bold; line-height: 15px;">
								<?php the_title(); ?>
							</div>
						</div>
					</div>
					</a>
					<?php endwhile; ?>
				</div>
			</div>
			<?php endif; ?>
		</div><!-- END CONTENT -->
		
		<?php get_sidebar('right_home'); ?>
		
<?php get_footer(); ?>