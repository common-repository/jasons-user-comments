<?php
/*
Plugin Name: Jason's User Comments
Version: 0.2
Plugin URI: http://noprerequisite.com/archives/2004/12/01/user-comments-plugin/
Description: Moderate comments not posted by registered users.
Author: Jason
Author URI: http://noprerequisite.com/
*/

add_action('comment_post','noprereq_users_only');
add_action('wp_head','noprereq_users_only_cache');
add_action('wp_footer','noprereq_users_only_output');

if (!function_exists('comment_user_id')){
	function comment_user_id() {
		global $comment;
		echo $comment->user_id;
	}
}

function is_user_comment() {
	global $comment;
	return $comment->user_id;
}

function noprereq_users_only($comment_id){
	global $wpdb, $approved, $user_ID;
	get_currentuserinfo();
	if (!$user_ID) {
		$wpdb->query("UPDATE $wpdb->comments SET comment_approved = '0' WHERE comment_ID='$comment_id'");
		$approved=0;
	} else {
		$wpdb->query("UPDATE $wpdb->comments SET comment_approved = '1' WHERE comment_ID='$comment_id'");
		$wpdb->query("UPDATE $wpdb->comments SET user_id = $user_ID WHERE comment_ID='$comment_id'");
		$approved=1;
	}
}

function noprereq_users_only_form($buffer){
	global $user_nickname, $user_email, $user_url;
	get_currentuserinfo();
	if ($user_nickname){
		$comment_author = isset($_COOKIE['comment_author_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_'.COOKIEHASH])) : '';
		$comment_author_email = isset($_COOKIE['comment_author_email_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_email_'.COOKIEHASH])) : '';
		$comment_author_url = isset($_COOKIE['comment_author_url_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_url_'.COOKIEHASH])) : '';         

		$old_author_field = '<input type="text" name="author" id="author" class="textarea" value="' . $comment_author . '" size="28" tabindex="1" />';
		$new_author_field = '<input type="text" name="author" id="author" class="textarea" value="' . $user_nickname . '" size="28" tabindex="1" readonly="readonly" />';
		$old_email_field = '<input type="text" name="email" id="email" value="' . $comment_author_email . '" size="28" tabindex="2" />';
		$new_email_field = '<input type="text" name="email" id="email" value="' . $user_email . '" size="28" tabindex="2" readonly="readonly" />';
		$old_url_field = '<input type="text" name="url" id="url" value="' . $comment_author_url . '" size="28" tabindex="3" />';
		$new_url_field = '<input type="text" name="url" id="url" value="' . $user_url . '" size="28" tabindex="3" readonly="readonly" />';

		$buffer = str_replace($old_author_field,$new_author_field,$buffer);
		$buffer = str_replace($old_email_field,$new_email_field,$buffer);
		$buffer = str_replace($old_url_field,$new_url_field,$buffer);
	}
	return $buffer;
}

function noprereq_users_only_cache(){
	if ( is_single() || is_page() || $withcomments ){
		ob_start("noprereq_users_only_form");
	}
}

function noprereq_users_only_output(){
	if ( is_single() || is_page() || $withcomments ){
		ob_end_flush();
	}
}
?>