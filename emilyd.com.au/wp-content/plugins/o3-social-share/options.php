<?php
	if(!current_user_can('manage_options'))
		wp_die(__('You do not have sufficient permissions to access this page.'));
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h2>O3 Social Share Options</h2>
<?php

    $hidden_field_name = 'o3_social_share_options_hidden';
    
    $network_name = 'o3_ss_networks';
    $network_val = get_option($network_name);
    if(!is_array($network_val)) {
    	$network_val = array();
    }
    
    $twitter_text_name = 'o3_twitter_tweet_text';
    $twitter_text_val = get_option($twitter_text_name);
    
    $twitter_text_custom_name = 'o3_twitter_tweet_text_custom';
    $twitter_text_custom_val = get_option($twitter_text_custom_name);
    
    $twitter_via_name = 'o3_twitter_via';
    $twitter_via_val = get_option($twitter_via_name);
    
    $twitter_recommend_name = 'o3_twitter_recommend';
    $twitter_recommend_val = get_option($twitter_recommend_name);
    
    $twitter_hashtag_name = 'o3_twitter_hashtag';
    $twitter_hashtag_val = get_option($twitter_hashtag_name);
    
    $facebook_send_name = 'o3_facebook_send';
    $facebook_send_val = get_option($facebook_send_name);
    if(!is_array($facebook_send_val)) {
    	$facebook_send_val = array();
    }
    
    $facebook_verb_name = 'o3_facebook_verb';
    $facebook_verb_val = get_option($facebook_verb_name);
    
    
    $o3_style_name = 'o3_ss_default_style';
    $o3_style_val = get_option($o3_style_name);
	if(!is_array($o3_style_val)) {
		$o3_style_val = array();
	}
	
	$o3_append_name = 'o3_ss_auto_append';
    $o3_append_val = get_option($o3_append_name);
    
    $o3_ss_pt_name = 'o3_ss_pt';
    $o3_ss_pt_val = get_option($o3_ss_pt_name);
	if(!is_array($o3_style_val)) {
		$o3_ss_pt_val = array();
	}    

    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        $network_val = $_POST[ $network_name ];
        if(!is_array($network_val)) {
	    	$network_val = array();
	    }
        update_option( $network_name, $network_val );
        
        $o3_style_val = $_POST[ $o3_style_name ];
        if(!is_array($o3_style_val)) {
	    	$o3_style_val = array();
	    }
        update_option( $o3_style_name, $o3_style_val );

	    $twitter_text_val = $_POST[$twitter_text_name];
	    update_option($twitter_text_name,$twitter_text_val);
	    
	    $twitter_text_custom_val = $_POST[$twitter_text_custom_name];
	    update_option($twitter_text_custom_name,$twitter_text_custom_val);
	    
	    $twitter_via_val = $_POST[$twitter_via_name];
	    update_option($twitter_via_name,$twitter_via_val);
	    
	    $twitter_recommend_val = $_POST[$twitter_recommend_name];
	    update_option($twitter_recommend_name,$twitter_recommend_val);
	    
	    $twitter_hashtag_val = $_POST[$twitter_hashtag_name];
	    update_option($twitter_hashtag_name,$twitter_hashtag_val);
	    
	    $facebook_send_val = $_POST[$facebook_send_name];
	    update_option($facebook_send_name,$facebook_send_val);
	    
	    if(!is_array($facebook_send_val)) {
	    	$facebook_send_val = array();
	    }
	    
	    $facebook_verb_val = $_POST[$facebook_verb_name];
	    update_option($facebook_verb_name,$facebook_verb_val);
	    
	    $o3_append_val = $_POST[ $o3_append_name ];
        update_option( $o3_append_name, $o3_append_val );
        
        $o3_ss_pt_val = $_POST[$o3_ss_pt_name];
	    update_option($o3_ss_pt_name,$o3_ss_pt_val);	    
	    if(!is_array($o3_ss_pt_val)) {
	    	$o3_ss_pt_val = array();
	    }
?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php
	
    }
?>
	<form name="form1" method="post" action="">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Active Networks:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Active Networks:</span>
						</legend>
						<p>
							<input id="twitter" type="checkbox" name="<?php echo $network_name; ?>[]" value="twitter" <?php echo(in_array("twitter",$network_val)) ? "checked" : ""; ?> /> <label for="twitter">Twitter</label><br />
							<input id="facebook" type="checkbox" name="<?php echo $network_name; ?>[]" value="facebook" <?php echo(in_array("facebook",$network_val)) ? "checked" : ""; ?> /> <label for="facebook">Facebook</label><br />
							<input id="googleplus" type="checkbox" name="<?php echo $network_name; ?>[]" value="googleplus" <?php echo(in_array("googleplus",$network_val)) ? "checked" : ""; ?> /> <label for="googleplus">Google+</label><br />
							<input id="linkedin" type="checkbox" name="<?php echo $network_name; ?>[]" value="linkedin" <?php echo(in_array("linkedin",$network_val)) ? "checked" : ""; ?> /> <label for="linkedin">LinkedIn</label><br />
						</p>
					</fieldset>
				</td>
			</tr>
			<tr id="twitter-options-row" valign="top" style="<?php echo(!in_array("twitter",$network_val)) ? "display: none;" : ""; ?>">
				<th scope="row">Twitter Options:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Twitter Options:</span>
						</legend>
						<p>
							<label>Tweet Text</label><br />
							<input id="tweet-text-title" type="radio" name="<?php echo $twitter_text_name; ?>" value="page_title" <?php echo($twitter_text_val == "page_title") ? "checked" : ""; ?> /> <label for="tweet-text-title">Use the title of the page</label><br />
							<input id="tweet-text-custom" type="radio" name="<?php echo $twitter_text_name; ?>" value="custom" <?php echo($twitter_text_val == "custom") ? "checked" : ""; ?> /> <input id="twitter-text-custom-input" type="text" name="<?php echo $twitter_text_custom_name; ?>" value="<?php echo $twitter_text_custom_val; ?>" <?php echo($twitter_text_val != "custom") ? "disabled" : ""; ?> />
						</p>
						<p>
							<label>Via</label>
							<input type="text" name="<?php echo $twitter_via_name; ?>" value="<?php echo $twitter_via_val; ?>" />
						</p>
						
						<p>
							<label>Recommend</label>
							<input type="text" name="<?php echo $twitter_recommend_name; ?>" value="<?php echo $twitter_recommend_val; ?>" />
						</p>
						
						<p>
							<label>Hashtag</label>
							<input type="text" name="<?php echo $twitter_hashtag_name; ?>" value="<?php echo $twitter_hashtag_val; ?>" />
						</p>
					</fieldset>
				</td>
			</tr>
			<tr id="facebook-options-row" valign="top" style="<?php echo(!in_array("facebook",$network_val)) ? "display: none;" : ""; ?>">
				<th scope="row">Facebook Options:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Facebook Options:</span>
						</legend>
						<p>
							<input id="facebook-send" type="checkbox" name="<?php echo $facebook_send_name; ?>[]" value="true" <?php echo(in_array("true",$facebook_send_val)) ? "checked" : ""; ?> /> <label for="facebook-send">Send Button</label><br />
						</p>
						<p>
							<label>Verb to display:</label><br />
							<input id="facebook-verb-1" type="radio" name="<?php echo $facebook_verb_name; ?>" value="like" <?php echo($facebook_verb_val == "like") ? "checked" : ""; ?> /> <label for="facebook-verb-1">Like</label><br />
							<input id="facebook-verb-2" type="radio" name="<?php echo $facebook_verb_name; ?>" value="recommend" <?php echo($facebook_verb_val == "recommend") ? "checked" : ""; ?> /> <label for="facebook-verb-2">Recommend</label><br />
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Include Default Styles:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Include Default Styles:</span>
						</legend>
						<p>
							<input id="o3-styles" type="checkbox" name="<?php echo $o3_style_name; ?>[]" value="true" <?php echo(in_array("true",$o3_style_val)) ? "checked" : ""; ?> /> <label for="o3-styles">Include</label><br />
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Automatically append social plugins to:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Automatically append social plugins to:</span>
						</legend>
						<p>
							<input id="o3-append-bottom" type="radio" name="<?php echo $o3_append_name; ?>" value="bottom" <?php echo($o3_append_val == "bottom") ? "checked" : ""; ?> /> <label for="o3-append-bottom">Bottom of Post Content</label><br />
							<input id="o3-append-top" type="radio" name="<?php echo $o3_append_name; ?>" value="top" <?php echo($o3_append_val == "top") ? "checked" : ""; ?> /> <label for="o3-append-top">Top of Post Content</label><br />
							<input id="o3-append-both" type="radio" name="<?php echo $o3_append_name; ?>" value="both" <?php echo($o3_append_val == "both") ? "checked" : ""; ?> /> <label for="o3-append-both">Top and Bottom of Post Content</label><br />
							<input id="o3-append-none" type="radio" name="<?php echo $o3_append_name; ?>" value="none" <?php echo($o3_append_val == "none") ? "checked" : ""; ?> /> <label for="o3-append-none">None. I will insert it manually using <code>echo o3_output_social_plugins()</code></label><br />
						</p>
						<p>If unchecking this, you can use <code>echo o3_output_social_plugins()</code> function in your template themes to manually place the social plugins.</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Apply Social Plugins to:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Apply Social Plugins to:</span>
						</legend>
						<p>
							<?php 
								$pts = get_post_types(array(),'objects');
								foreach($pts as $pt) {
							?>
								<input id="o3-pt-<?=$pt->name?>" type="checkbox" name="<?php echo $o3_ss_pt_name; ?>[]" value="<?=$pt->name?>" <?php echo(in_array($pt->name,$o3_ss_pt_val)) ? "checked" : ""; ?> /> <label for="o3-pt-<?=$pt->name?>"><?=$pt->name?></label><br />
							<?php
								}
							?>
						</p>
					</fieldset>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
	
	</form>
	
	<h3>Like This Plugin?</h3>
	<p>Don't worry, we're not asking for money. But we would appreciate a friendly Tweet, Like or +</p>
	<p>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=126445887469807";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<div class="fb-like" data-href="https://www.facebook.com/WeAreO3/" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>
	</p>
	<p>
		<a href="https://twitter.com/WeAreO3" class="twitter-follow-button" data-show-count="false">Follow @WeAreO3</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</p>
	<p>
		<!-- Place this tag where you want the +1 button to render -->
<g:plusone size="medium" href="http://www.weareo3.com"></g:plusone>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
	</p>
	
</div>
<script type="text/javascript">
jQuery(function() {
	jQuery('input[name=<?=$twitter_text_name?>]').on('change',function() {
		if(jQuery(this).val() == "custom") {
			jQuery('#twitter-text-custom-input').removeAttr('disabled');
		} else {
			jQuery('#twitter-text-custom-input').attr('disabled','disabled');
		}
	});
	
	jQuery('#twitter').on('change',function() {
		if(jQuery(this).is(':checked')) {
			$j('#twitter-options-row').show();
		} else {
			$j('#twitter-options-row').hide();
		}
	});
	
	jQuery('#facebook').on('change',function() {
		if(jQuery(this).is(':checked')) {
			$j('#facebook-options-row').show();
		} else {
			$j('#facebook-options-row').hide();
		}
	});
});
</script>