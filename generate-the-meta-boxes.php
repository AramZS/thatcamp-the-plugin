<?php

//Based on code at http://wefunction.com/2008/10/tutorial-creating-custom-write-panels-in-wordpress/

$og_link_new_meta_box =
	array(
		"oglink" => array(
		
			"name" => "oglink",
			"std" => "",
			"description" => "Add a link to the webpage you want Open Graph data from."
			),
//You can add more fields to this box just by adding to the array.
		"seotitle" => array(
			"name" => "ogposttitle",
			"std" => "",
			"description" => "OG Post Title."
			)
	);

function og_link_new_meta_boxes() {

	global $post, $og_link_new_meta_box;
	foreach($og_link_new_meta_box as $meta_box){
		$meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
		
		if($meta_box_value == ""){
			$meta_box_value=$meta_box['std'];
		}
	

		echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		echo'<p><input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" size="55" /><br />';
		echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label></p>';
		
	}
}

function og_link_create_meta_box() {
	if ( function_exists('add_meta_box') ) {
		add_meta_box( 'og_link_new_meta_box', 'Open Graph Link and More', 'og_link_new_meta_boxes', 'post', 'normal', 'high' );
	}
}

function og_link_save_postdata( $post_id ) {
	global $post, $og_link_new_meta_box;

	foreach($og_link_new_meta_box as $meta_box) {
		if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ))
			return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ))
			return $post_id;
		}

		$data = $_POST[$meta_box['name']];

		if(get_post_meta($post_id, $meta_box['name']) == "")
			add_post_meta($post_id, $meta_box['name'], $data, true);
		elseif($data != get_post_meta($post_id, $meta_box['name'], true))
			update_post_meta($post_id, $meta_box['name'], $data);
		elseif($data == "")
		delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
	}
}
add_action('admin_menu', 'og_link_create_meta_box');
add_action('save_post', 'og_link_save_postdata');

?>