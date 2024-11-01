<?php
/*
Plugin Name: Twitter Content Locker
Plugin URI: http://www.icprojects.net/twitter-content-locker.html
Description: The plugin allows to lock part of post/page. User must share it on Twitter to view the content. Please visit official <a target="_blank" href="http://www.icprojects.net/twitter-content-locker.html">plugin page</a> for more info.
Version: 1.28
Author: Ivan Churakov
Author URI: http://www.icprojects.net/about
*/
define('TWITTERLOCKER_DEFAULTMESSAGE', 'Share this page on Twitter and view full content!');
define('TWITTERLOCKER_COOKIEVALUE', '0001');

wp_enqueue_script("jquery");

class twitterlocker_class {
	
	function __construct() {
		if (is_admin()) {
			add_filter('mce_external_plugins', array(&$this, "mce_external_plugin"));
			add_filter('mce_buttons', array(&$this, "mce_button"), 0);
			add_action('wp_ajax_twitterlocker', array(&$this, "twitterlocker_callback"));
			add_action('wp_ajax_nopriv_twitterlocker', array(&$this, "twitterlocker_callback"));
		} else {
			wp_deregister_script('twittersdk');
			wp_register_script('twittersdk', 'https://platform.twitter.com/widgets.js');
			wp_enqueue_script("twittersdk");
			add_action("wp_head", array(&$this, "front_header"));
			add_shortcode('twitterlocker', array(&$this, "shortcode_handler"));
		}
	}

	function mce_button($buttons) {
		array_push($buttons, "separator", "twitterlockerplugin");
		return $buttons;
	}

	function mce_external_plugin($plugin_array){
		$url = plugins_url('/js/button.js', __FILE__);
		$plugin_array['twitterlockerplugin'] = $url;
		return $plugin_array;
	}

	function twitterlocker_callback() {
		global $wpdb;
		$post_id = $_POST['post'];
		setcookie("twitterlocker_".$post_id, TWITTERLOCKER_COOKIEVALUE, time()+3600*24*90, "/");
	}	

	function front_header() {
		echo '
		<link type="text/css" rel="stylesheet" href="'.plugins_url('/css/style.css', __FILE__).'">
		<script type="text/javascript">
			var twitterlocker_use = false;
			jQuery(document).ready(function() {
				twttr.ready(function (twttr) {
					twttr.events.bind("tweet", function(event) {
						var data = {post: "'.get_the_ID().'", action: "twitterlocker"};
						jQuery.post("'.admin_url('admin-ajax.php').'", data, function(response) {
							if (twitterlocker_use) location.reload();
						});
					});
				});
			});
		</script>';
	}

	function shortcode_handler($_atts, $content=null) {
		global $wpdb;
		
		if (isset($_atts["message"])) $message = $_atts["message"];
		else $message = TWITTERLOCKER_DEFAULTMESSAGE;

		$post_id = get_the_ID();
		
		$post_url = get_permalink($post_id).'#'.mt_rand(0, mt_getrandmax ());
		$cookie_value = $_COOKIE["twitterlocker_".$post_id];
		
		if($cookie_value == TWITTERLOCKER_COOKIEVALUE) $content = do_shortcode($content).'<br />';
		else {
			$content = '
				<script type="text/javascript">
					twitterlocker_use = true;
				</script>
				<div class="twitterlocker-box">
					'.$message.'
					<div><a data-related="webtemplates" href="http://twitter.com/share" class="twitter-share-button" data-text="" data-url="'.$post_url.'" data-count="horizontal">'.__('Tweet', 'twitterlocker').'</a></div>
				</div>';
		}
		return $content;
	}
	
}
$twitterlocker = new twitterlocker_class();
?>