<div id="rbMenu" style="display: none;">
	<div class="element">
		<h3><i class="icon-retweet icon-white"></i> Share</h3>
		<ul>
			<a href="http://www.twitter.com/share?url=<?php echo wz_get_current_encoded_url(); ?>" target="_blank" rel="nofollow"><li>
				Twitter
			</li></a>
			<a href="http://www.facebook.com/sharer.php?u=<?php echo wz_get_current_encoded_url(); ?>&t=<?php wp_title(); ?>" target="_blank"><li>
				Facebook
			</li></a>
			<a href="mailto:?subject=Hey, check this out!&body=<?php echo single_post_title('',false)."\n".wz_get_current_encoded_url(); ?>" target="_blank"><li>
				E-Mail
			</li></a>
		</ul>
	</div>
	<div class="element">
		<h3>Manage</h3>
		<?php wp_footer(); ?>
	</div>
</div><!-- END RBMENU -->