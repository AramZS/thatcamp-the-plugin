<?php

function plugin_style_setup() {

	//Want to replace the default styling applied to tweets? Just add 'your-plugin-style.css' to your stylesheet's directory.
	//This function tries to find a user override stylesheet. 
	$theme_style_file = get_stylesheet_directory() . '/your-plugin-style.css';

	//If you've added a user override CSS, then it will be used instead of the styling I included in the plugin.
	//Check to see if the file exists
	if ( file_exists($theme_style_file) ) { //if so...
		//register the style in WordPress, giving it a name. 
		wp_register_style( 'thatcamp-plugin-style', $theme_style_file );
		//add it to WordPress using a function that is safe and checks dependencies.
		wp_enqueue_style( 'thatcamp-plugin-style' );
		//For more see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	}
	//If not, you get the default styling. 
	else {
		wp_register_style( 'our-plugin-style', plugins_url('thatcamp-style.css', __FILE__) );
		wp_enqueue_style( 'our-plugin-style' );
	}

}
//Add it to the function that loads when your WordPress page loads. 
add_action( 'wp_enqueue_scripts', 'plugin_style_setup', 1 );

?>