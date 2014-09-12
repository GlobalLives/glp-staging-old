<?php global $activity; //$activity_user = get_userdata($activity['activity_user']); ?>
<!-- <?php var_dump($activity); ?> -->
	<li class="activity <?php echo $activity['activity_type']; ?> row">
		<div class="activity-thumbnail col-md-2"><img src="<?php the_profile_thumbnail_url($activity['activity_user'],'medium'); ?>"></div>
		<div class="activity-meta">
			<span class="activity-username">@<?php echo $activity_user->user_login; ?></span>
			<?php echo $activity['activity_description']; ?>
			<?php echo human_time_diff( $activity['activity_timestamp'], current_time('timestamp') ); ?> ago.
		</div>
		<div class="activity-content"><?php echo $activity['activity_content']; ?></div>
	</li>
