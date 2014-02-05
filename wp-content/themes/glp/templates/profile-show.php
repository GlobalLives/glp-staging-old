<?php global $profile, $current_user; ?>
<article id="user-<?php echo $profile->ID; ?>" class="container">
	<header class="row">
		<div class="profile-header span9 offset3">
			<div class="profile-header-inner">
				<?php if ($current_user->ID == $profile->ID) : ?><a class="edit-profile" href="/profile?profile-edit=1"><?php _e('Edit','glp'); ?> <i class="icon icon-white icon-edit"></i></a><?php endif; ?>
				<h1 class="profile-name"><?php echo $profile->first_name; ?> <?php echo $profile->last_name; ?></h1>
				<p class="profile-location"><b><?php the_field('user_occupation','user_'.$profile->ID); ?></b> <?php _e('in','glp'); ?> <b><?php the_field('user_location','user_'.$profile->ID); ?></b></p>
				<div class="profile-username">@<?php echo $profile->user_login; ?></div>
			</div>
		</div>
	</header>

	<div class="profile-container row">
		<div class="profile-sidebar span3">
			<div class="profile-sidebar-inner">
				<div class="profile-thumbnail"><img src="<?php the_profile_thumbnail_url($profile->ID,'medium'); ?>"></div>
				<p><b><?php _e('Member since','glp'); ?>:</b> <?php echo date("F Y", strtotime($profile->user_registered)); ?></p>
				<p><b><?php _e('Last activity','glp'); ?>:</b> <?php echo human_time_diff( get_profile_last_active( $profile->ID ), current_time('timestamp') ); ?> ago.</p>
				<hr>

				<?php if (get_field('user_skills','user_'.$profile->ID)) : ?>
				<p>
					<b><?php _e('Volunteer Skills','glp'); ?></b><br>
				<?php while (has_sub_field('user_skills','user_'.$profile->ID)) : ?>
					<?php if (get_sub_field('skill_name')) : ?><span class="skill_name"><?php the_sub_field('skill_name'); ?></span> <span class="skill_level"><?php $skill_level = get_sub_field('skill_level'); for ($i = 0; $i < $skill_level; $i++) :?>&bull;<?php endfor; ?></span><br><?php endif; ?>
				<?php endwhile; ?>
				</p>
				<?php endif; ?>

				<?php if (get_field('user_languages','user_'.$profile->ID)) : ?>
				<p>
					<b><?php _e('Languages Spoken','glp'); ?></b><br>
				<?php while (has_sub_field('user_languages','user_'.$profile->ID)) : ?>
					<?php if (get_sub_field('language_name')) : ?><span class="skill_name"><?php the_sub_field('language_name'); ?></span> <span class="skill_level"><?php $language_level = get_sub_field('language_level'); for ($i = 0; $i < $language_level; $i++) :?>&bull;<?php endfor; ?></span><br><?php endif; ?>
				<?php endwhile; ?>
				</p>
				<?php endif; ?>

				<?php if (get_field('user_contact','user_'.$profile->ID)) : ?>
				<p>
					<b><?php _e('Contact Information','glp'); ?></b><br>
				<?php while (has_sub_field('user_contact','user_'.$profile->ID)) : ?>
					<i class="fa fa-<?php echo strtolower(get_sub_field('contact_channel')); ?>"></i>
					<?php the_sub_field('contact_information'); ?><br>
				<?php endwhile; ?>
				</p>
				<?php endif; ?>

			</div>
		</div>

		<div class="profile-body span9">
			<div class="profile-body-inner">
				<h4><?php _e('Bio','glp'); ?></h4>
				<p><?php echo $profile->description; ?></p>
			<?php if ($profile->user_url) : ?>
				<h4><?php _e('Website','glp'); ?></h4>
				<p><?php echo $profile->user_url; ?></p>
			<?php endif; ?>
				<p class="profile-activity-buttons">
					<span class="span1"><?php _e('Activity','glp'); ?></span>
					<a class="" href=""><i class="fa fa-video-camera"></i> Shoots</a>
					<a class="" href=""><i class="fa fa-comment"></i> Comments</a>
					<a class="" href=""><i class="fa fa-tag"></i> Tags</a>
					<a class="" href="">@ Mentions</a>
					<a class="" href=""><i class="fa fa-bookmark"></i> Bookmarks</a>
					<a class="" href=""><i class="fa fa-heart"></i> Favorites</a>
				</p>
								
				<ul class="profile-activity">
				<?php foreach( get_profile_activities( $profile->ID ) as $activity ) : $activity_user = get_userdata( $activity['activity_user'] ); ?>
					<li class="activity <?php echo $activity['activity_type']; ?> row">
						<div class="activity-thumbnail span1"><img src="<?php the_profile_thumbnail_url($activity['activity_user']); ?>"></div>
						<div class="activity-meta span7">
							<i class="fa fa-<?php echo $activity['activity_icon']; ?>"></i>
							<span class="activity-username"><?php the_fullname($activity_user->ID); ?></span> 
							<?php echo $activity['activity_description']; ?> 
							<?php echo human_time_diff( $activity['activity_timestamp'], current_time('timestamp') ); ?> ago.
						</div>
						<div class="activity-content span6"><?php echo $activity['activity_content']; ?></div>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</article>