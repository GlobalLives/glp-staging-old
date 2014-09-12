<?php

function get_participant_summary() {
	query_posts(array('post_type' => 'participant', 'p' => $_POST['post_id']));
	$response = get_template_part('templates/participant', 'summary');
	echo $response;
	exit;
}
add_action('wp_ajax_nopriv_get_participant_summary', 'get_participant_summary');
add_action('wp_ajax_get_participant_summary', 'get_participant_summary');

function get_participant_clip() {
	query_posts(array('post_type' => 'clip', 'p' => $_POST['clip_id']));
	$response = get_template_part('templates/clip', 'stage');
	echo $response;
	exit;
}
add_action('wp_ajax_nopriv_get_participant_clip', 'get_participant_clip');
add_action('wp_ajax_get_participant_clip', 'get_participant_clip');

function toggle_clip() {
	$user_id = (int) $_POST['user_id'];
	$clip_id = (int) $_POST['clip_id'];
	$response = "";

	$queue = get_participant_field('library', $user_id);
	$queued = is_clip_in_library($clip_id, $user_id);

	if ($queue && (isset($queued))) {
			unset($queue[$queued]);
			$response = apply_filters('clip_toggle_response', $response, false);
	} else {
			$queue[] = $clip_id;
			$response = apply_filters('clip_toggle_response', $response, true);
	}
	update_participant_field('library', apply_filters('clean_queue', $queue), $user_id);

	echo $response;
	exit;
}
add_action('wp_ajax_nopriv_toggle_clip', 'toggle_clip');
add_action('wp_ajax_toggle_clip', 'toggle_clip');

function toggle_clip_list() {
	$user_id = (int) $_POST['user_id'];
		$post_id = (int) $_POST['post_id'];

		$clips = get_field('clips', $post_id);
		$queue_key = apply_filters('queue_key', $queue_key, 'library');
		$queue = get_field( $queue_key, 'user_'.$user_id );
		$all_queued = is_list_in_library($clips, $user_id);

	if ( true === $all_queued )  { // If all items are queued, we must be removing them all
			foreach ($clips as $clip) {
				$queued = array_search($clip, $queue);
				$toggled[] = $clip->ID;
				unset($queue[$queued]);
			}
			$response = json_encode( array(
				'text' => apply_filters( 'clip_toggle_list_response', $response, false, 'library' ),
				'toggled' => $toggled
			));
	}
		else { //Otherwise we add the missing ones to the end of the queue
			foreach ( $all_queued as $clip ) {
				$queue[] = $clip->ID;
				$toggled[] = $clip->ID;
			}
			$response = json_encode( array(
				'text' => apply_filters( 'clip_toggle_list_response', $response, true, 'library' ),
				'toggled' => $toggled
			));
	}

	update_field( $queue_key, apply_filters('clean_queue', $queue), 'user_'.$user_id );
	echo $response;
	exit;
}
add_action( 'wp_ajax_nopriv_toggle_clip_list', 'toggle_clip_list' );
add_action( 'wp_ajax_toggle_clip_list', 'toggle_clip_list' );

function clip_status() {
	$user_id = (int) $_POST['user_id'];
	$clip_id = (int) $_POST['clip_id'];

	echo json_encode( array(
		'clip_id' => $clip_id,
		'status' => apply_filters('clip_toggle_library_status', $text, $clip_id, $user_id)
	) );
	exit;
}
add_action( 'wp_ajax_nopriv_clip_status', 'clip_status' );
add_action( 'wp_ajax_clip_status', 'clip_status' );

function clip_submit_comment() {

	$time = current_time('mysql');
	$clip_id = (int) $_POST['post_id'];
	$user = wp_get_current_user();
	$comment = sanitize_text_field($_POST['comment']);
	$minutes = (int) $_POST['minutes'];
	$seconds = (int) $_POST['seconds'];
	$position = (int) $_POST['position'];

	if ( !$clip_id )
		$r['message'] = sprintf('<p class="error">%s</p>' ,__("There was an error", 'glp') );
	if ( !$user->ID )
		$r['message'] = sprintf('<p class="error">%s</p>' ,__("You need to be logged in to comment", 'glp') );
	if ( !$comment )
		$r['message'] = sprintf('<p class="error">%s</p>' ,__("A comment or tag (#tag) is required", 'glp') );
	if ( ! ($minutes || $seconds) )
		$r['message'] = sprintf('<p class="error">%s</p>' ,__("A time is required", 'glp') );

	// Bail if we have an error
	if ($r['message']) {
		$r['success'] = 0;
		echo json_encode($r);
		exit;
	}

	$data = array(
		'comment_post_ID' => $clip_id,
		'comment_author' => $user->user_login,
		'comment_author_email' => $user->user_email,
		'comment_author_url' => $user->user_url,
		'comment_content' => $comment,
		'user_id' => $user->ID,
		'comment_date' => $time,
		'comment_approved' => 1,
		'comment_author_IP' => $_SERVER['REMOTE_ADDR']
	);

	remove_filter('get_comment', 'style_hashtags');
	$comment_id = wp_insert_comment($data);
	add_filter('get_comment', 'style_hashtags');
	$comment_meta = update_comment_meta( $comment_id, 'clip_time', array('m' => $minutes, 's' => $seconds, 'p' => $position ) );
	if ($comment_id && $comment_meta) {
		$comment = get_comment($comment_id);
		ob_start();
		include(locate_template('templates/marker-comment.php'));
		$r['message'] = ob_get_clean();
		$r['success'] = $position;
	}

	echo json_encode($r);
	exit;
}
add_action( 'wp_ajax_nopriv_clip_submit_comment', 'clip_submit_comment' );
add_action( 'wp_ajax_clip_submit_comment', 'clip_submit_comment' );

function glp_register_user() {
	if( !isset( $_POST['user_nonce'] ) || !wp_verify_nonce( $_POST['user_nonce'], 'glp_user_registration' )) { die('ERROR!'); }

	$userdata = array(
		'user_login' => sanitize_user($_POST['user_login'], true),
		'user_pass'  => sanitize_text_field($_POST['user_pass']),
		'user_email' => sanitize_email($_POST['user_email'])
	);

	$user_id = wp_insert_user($userdata) ;

	if(!is_wp_error($user_id)) {
		echo '1';
	} else {
		echo $user_id->get_error_message();
	}
  die();
}
add_action('wp_ajax_nopriv_glp_register_user', 'glp_register_user');
add_action('wp_ajax_glp_register_user', 'glp_register_user');