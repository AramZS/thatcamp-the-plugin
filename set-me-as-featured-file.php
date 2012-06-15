<?php

function set_me_as_featured($postID,$img,$name){
	
	//Methods within sourced from http://codex.wordpress.org/Function_Reference/wp_insert_attachment
	//and http://wordpress.stackexchange.com/questions/26138/set-post-thumbnail-with-php
	
	//Get the type of the image file. .jpg, .gif, or whatever
	$filetype = wp_check_filetype( $img );

	//Set the identifying variables for the about to be featured image.
	$imgData = array(
					//tell WordPress what the filetype is. 
					'post_mime_type' => $filetype['type'],
					//set the image title to the title of the site you are pulling from
					'post_title' => $name,
					//WordPress tells us we must set this and set it to empty. Why? Dunno.
					'post_content' => '',
					//Now we set the status of the image. It will inheret that of the post.
					//If the post is published, then the image will be to.
					'post_status' => 'inherit'
				);
	//WordPress needs an absolute path to the image, as opposed to the relative path we used before. 
	$pathedImg = ABSPATH . $img;
	//Now we insert the image as a WordPress attachement, and associate it with the current post. 
	$thumbid = wp_insert_attachment($imgData, $pathedImg, $postID);
	
	//To set a thumbnail, you need metadata associated with an image. 
	//To get that we need to call the image.php file
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$metadata = wp_generate_attachment_metadata( $thumbid, $pathedImg );
	//Now we attach the meta data to the image.
	wp_update_attachment_metadata( $thumbid, $metadata );
	
	//Now that we have a correctly meta-ed and attached image we can finally turn it into a post thumbnail.
	update_post_meta($postID, '_thumbnail_id', $thumbid);


}

?>