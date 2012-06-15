<?php

/*
Plugin Name: THATCamp the Plugin
Plugin URI: http://aramzs.me
Description: This plugin is a tutorial for THATCamp. 
Version: 0.2
Author: Aram Zucker-Scharff
Author URI: http://aramzs.me
License: GPL2
*/

/*  Copyright 2012  Aram Zucker-Scharff  (email : azuckers@gmu.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//use this for testing when things go wrong:
//	print_r($troubleVariable); 
//	die();

//This adds the library we're going to use to pull and parse Open Graph data from a page. 
require_once("OpenGraph.php");

//Here's the function to set up the input boxes on your post screen. 
include 'generate-the-meta-boxes.php';

//We're going to store the set_me_as_featured() function in this external file. 
include 'set-me-as-featured-file.php';

//Here's where we will pull in the function to pull relevent tweets, called get_some_link_relevent_tweets().
include 'get-some-tweets.php';

//Here's the code to set up a menu to control settings. 

function og_additive($content) {
	if( is_singular() && is_main_query() ) {

		$postID = get_the_ID();

		add_post_meta($postID, 'opengraph_image_cache', '', true);
		add_post_meta($postID, 'opengraph_title_cache', '', true);
		add_post_meta($postID, 'opengraph_descrip_cache', '', true);

		$checkogcache = get_post_meta($postID, 'opengraph_image_cache', true);
		
		$oguserlink = get_post_meta($postID, 'oglink', true);
	
/** for testing 
			$page = $oguserlink;
			$node = OpenGraph::fetch($page);
			
			$ogImage = $node->image;
			$ogTitle = $node->title;
			$ogDescrip = $node->description;
			print_r( $node );
			die();
**/	
		
		
		if (empty($checkogcache)){

			$page = $oguserlink;
			
			$node = OpenGraph::fetch($page);
			
			$ogImage = $node->image;
			$ogTitle = $node->title;
			$ogDescrip = $node->description;
			
			update_post_meta($postID, 'opengraph_title_cache', $ogTitle);
			update_post_meta($postID, 'opengraph_descrip_cache', $ogDescrip);
			


			
			
			if ( (strlen($ogImage)) > 0 ){
			
				$imgParts = pathinfo($ogImage);
				$imgExt = $imgParts['extension'];
				$imgTitle = $imgParts['filename'];

				
				//'/' . get_option(upload_path, 'wp-content/uploads') . '/' . date("o") 
				$uploads = wp_upload_dir();
				$ogCacheImg = 'wp-content/uploads' . $uploads[subdir] . "/" . $postID . "-" . $imgTitle . "." . $imgExt;
				
				
				if ( !file_exists($ogCacheImg) ) {
				

					copy($ogImage, $ogCacheImg);
				
				}
				//$ogCacheImg = $ogImage;
				
			} else {
			
				$oglinkpath = plugin_dir_url(__FILE__);
			
				$ogCacheImg = $oglinkpath . 'link.png';
			
			}
			
			update_post_meta($postID, 'opengraph_image_cache', $ogCacheImg);
			
		} else {
		
			$ogCacheImg = get_post_meta($postID, 'opengraph_image_cache', true);
			
		}
		
		
		
		$ogCacheImg = get_post_meta($postID, 'opengraph_image_cache', true);
		$ogTitle = get_post_meta($postID, 'opengraph_title_cache', true);
		$ogDescrip = get_post_meta($postID, 'opengraph_descrip_cache', true);
		
		//Let's get the current WordPress version. We'll need it later. 
		$wpver = get_bloginfo('version');
		
		//Once again, we are going to need to know what version WP is
		//in order to know how to tream the output of get_some_link_relevent_tweets
		//More info on this in get-some-tweets.php, line 65.
		$wpver = get_bloginfo('version');
		$floatWPVer = floatval($wpver);
		
		get_some_link_relevent_tweets($postID, $oguserlink);
		$tweetone = get_post_meta($postID, 'related_tweet_one', true);
		$tweettwo = get_post_meta($postID, 'related_tweet_two', true);
		
		if ($floatWPVer >= 3.4){
		
			//This calls WordPress's open embed function in order to feed it the Twitter links.
			//Then stores the generated embed code back in the needed variables.
			//If you want it anything other than the default width of your content.
			//You'll need to send some args here, see http://codex.wordpress.org/Embeds
			$tweetone = wp_oembed_get($tweetone);
			$tweettwo = wp_oembed_get($tweettwo);

		}
		
		if (!empty($ogCacheImg)){
			set_me_as_featured($postID, $ogCacheImg, $ogTitle);
		}

		$new_content = 
		'<div class="oglinkbox">
			<div class="oglinkimg">
				<a href="' . $oguserlink . '" title="' . $ogTitle . '"><img alt="' .  $ogTitle . '" src="' . $ogCacheImg . '" /></a>
			</div>
			<div class="oglinkcontent">
				<h4><a href="' . $oguserlink . '" title="' . $ogTitle . '">' . $ogTitle . '</a></h4>
				<p>' . $ogDescrip . '</p>
			</div>
			<div class="og-related-tweets">
			' . $tweetone . '
			' . $tweettwo . '
			</div>
		</div>';
			
		$content .= $new_content;	
	}	
	return $content;
}
add_filter('the_content', 'og_additive', 2);

function syndication_additive() {
	if( is_singular() && is_main_query() ) {

		$postID = get_the_ID();
		
		$oguserlink = get_post_meta($postID, 'oglink', true);
	
		echo '<meta name="syndication-source" content="' . $oguserlink . '"/>'; 
		
	}
	
}
add_action('wp_head', 'syndication_additive');

include 'set-up-a-style.php';

?>