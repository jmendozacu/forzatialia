<?php
/*
Plugin Name: O3 Social Share
Plugin URI: http://www.WeAreO3.com/plugins/o3-social-share/
Description: This plugin creates share boxes for Facebook, Twitter, Google+, and LinkedIn
Version: 1.1.3
Author: O3
Author URI: http://www.WeAreO3.com
License: GPL2
*/

function o3_output_social_plugins($content="") {
	global $post;
	//o3 social plugin options
    $networks = get_option('o3_ss_networks');
    if(!is_array($networks)) {
    	$networks = array();
    }
    $twitter_text = get_option('o3_twitter_tweet_text');
    $twitter_text_custom = get_option('o3_twitter_tweet_text_custom');
    $twitter_via = get_option('o3_twitter_via');
    if(substr($twitter_via,0,1) == "@") {
    	$twitter_via = substr($twitter_via,1);
    }
    $twitter_recommend = get_option('o3_twitter_recommend');
    if(substr($twitter_recommend,0,1) == "@") {
    	$twitter_recommend = substr($twitter_recommend,1);
    }
    
    $twitter_hashtag = get_option('o3_twitter_hashtag');
    if(substr($twitter_hashtag,0,1) == "#") {
    	$twitter_hashtag = substr($twitter_hashtag,1);
    }
    
    $facebook_send = get_option('o3_facebook_send');
    if(!is_array($facebook_send)) {
    	$facebook_send = array();
    }
    
    $facebook_verb = get_option('o3_facebook_verb');
	
	$auto_append = get_option('o3_ss_auto_append');
	
	$post_types = get_option('o3_ss_pt');
	if(!is_array($post_types)) {
		$post_types = array();
	}
	
	$ss = "";
	if(in_array($post->post_type,$post_types) || $auto_append == "none" ) {
		$ss .= '<div id="o3-social-share">';
		$url = get_permalink($post->ID);
		if(in_array("twitter",$networks)) {
			$ss .= '
				<div class="twitter">
					<a href="https://twitter.com/share" class="twitter-share-button" ' . ($twitter_text_custom != "" ? 'data-text="' . $twitter_text_custom . '"' : "") . ' ' . ($twitter_via != "" ? 'data-via="' . $twitter_via . '"' : "") . ' ' . ($twitter_recommend != "" ? 'data-related="' . $twitter_recommend . '"' : "") . ' ' . ($twitter_hashtag != "" ? 'data-hashtags="' . $twitter_hashtag . '"' : "") . ' data-url="' . $url . '">Tweet</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</div>
			';
			
		}
		
		if(in_array("facebook",$networks)) {
			$ss .= '
			<div class="facebook">
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=126445887469807";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, "script", "facebook-jssdk"));</script>	
				<div class="fb-like" data-href="' . $url . '" ' . (in_array("true",$facebook_send) ? 'data-send="true"' : 'data-send="false"') . ' data-layout="button_count" ' . ($facebook_verb == "recommend" ? 'data-action="recommend"' : '') . ' data-show-faces="false"></div>		
			</div>
			';
		}
		
		if(in_array("googleplus",$networks)) {
			$ss .= '
				<div class="googleplus">
					<!-- Place this tag where you want the +1 button to render -->
					<g:plusone size="medium" href="' . $url . '"></g:plusone>
					
					<!-- Place this render call where appropriate -->
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
					    po.src = "https://apis.google.com/js/plusone.js";
					    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
				</div>
			';
		}
		
		if(in_array("linkedin",$networks)) {
			$ss .= '
				<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
				<script type="IN/Share" data-url="' . $url . '" ' . ($style == "count" ? 'data-counter="right"' : '') . '></script>
			';
		}

		$ss .= '</div>';
		if(is_singular($post_types)) {
			if($auto_append == "top") {
				return $ss . $content;
			} elseif($auto_append == "bottom") {
				return $content . $ss;
			} elseif($auto_append == "both") {
				return $ss . $content . $ss;
			} elseif($auto_append == "none") {
				return $ss;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
	} else {
		return $content;
	}
}

$auto_append = get_option('o3_ss_auto_append');
if($auto_append != "none") {
	add_action('the_content', 'o3_output_social_plugins',20);
}

function create_o3_social_share_options() {
	add_submenu_page('options-general.php','O3 Social Share','O3 Social Share','manage_options','o3_social_share_options','create_o3_social_share_options_page');
}
add_action('admin_menu','create_o3_social_share_options');

function create_o3_social_share_options_page() {
	include(plugin_dir_path(__FILE__) . '/options.php');
}

function o3_social_share_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin) {
		$settings_link = '<a href="options-general.php?page=o3_social_share_options">'.__("Settings", "o3-social-share").'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'o3_social_share_settings_link', 10, 2 );


function o3_social_share_activate() {
    if(get_option('o3_ss_networks') == "")
    	update_option('o3_ss_networks',array('twitter','facebook','googleplus','linkedin'));
    	
	if(get_option('o3_twitter_tweet_text') == "")
		update_option('o3_twitter_tweet_text','page_title');

    if(get_option('o3_facebook_send') == "")
    	update_option('o3_facebook_send','false');
    
    if(get_option('o3_facebook_verb') == "")
    	update_option('o3_facebook_verb','like');
    
    if(get_option('o3_ss_style') == "")
    	update_option('o3_ss_style','count');
    
    if(get_option('o3_ss_default_style') == "")
    	update_option('o3_ss_default_style',array('true'));
    
    if(is_array('o3_ss_auto_append')) {
    	delete_option('o3_ss_auto_append');
    }
    
    if(get_option('o3_ss_auto_append') == "")
    	update_option('o3_ss_auto_append','bottom');
    	
    if(get_option('o3_ss_pt') == "" || !is_array(get_option('o3_ss_pt')))
    	update_option('o3_ss_pt',array('post'));
}
register_activation_hook(__FILE__,'o3_social_share_activate');

function o3_social_share_uninstall() {
	delete_option('o3_ss_networks');
    delete_option('o3_twitter_tweet_text');
    delete_option('o3_twitter_tweet_text_custom');
    delete_option('o3_twitter_via');
    delete_option('o3_twitter_recommend');
    delete_option('o3_twitter_hashtag');
    delete_option('o3_facebook_send');
    delete_option('o3_facebook_verb');
    delete_option('o3_ss_style');
    delete_option('o3_ss_default_style');
    delete_option('o3_ss_auto_append');
    delete_option('o3_ss_pt');
}
register_uninstall_hook(__FILE__,'o3_social_share_uninstall');

function o3_styles() {
	$show_styles = get_option('o3_ss_default_style');
	if(is_array($show_styles) && in_array("true",$show_styles)) {
		wp_register_style('o3-ss-styles',plugin_dir_url(__FILE__) . 'o3-social-share-styles.css');
		wp_enqueue_style('o3-ss-styles');
	}
}
add_action('init','o3_styles');


?>