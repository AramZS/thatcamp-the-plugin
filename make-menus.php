<?php

//Based on code at http://ottopress.com/2009/wordpress-settings-api-tutorial/
//Uses WordPress's Plugin API
// add the admin options page
add_action('admin_menu', 'thatcamp_admin_add_page');
function thatcamp_admin_add_page() {
	//Add that options page title, menu item, user capability, the slug to refer to, the output function. 
	add_options_page('THATCamp 2012 Plugin Options', 'THATCamp Options', 'manage_options', 'tc', 'tc_options_page');
}

// display the admin options page
function tc_options_page() {
	?>
	<div>
	<h2>Twitter Archival Shortcode Options</h2>
	Options and documentation relating to the Twitter Archival Shortcode plugin.
	<form action="options.php" method="post">
	<?php settings_fields('tc_options'); ?>
	<?php do_settings_sections('thatcamp_checks'); ?>
	<br />
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
	
	<h3>Hope you found this workshop useful!</h3>
	<?php
}

// add the admin settings and such
add_action('admin_init', 'tc_admin_init');

function tc_admin_init(){
	//the name of the group, the name of the option (array), the name of the validation function.
	register_setting( 'tc_options', 'tc_options', 'tc_options_validate' );
	//Name of the section, title of the section, call to generate the section title, menu page association
	add_settings_section('tc_main', 'Main Settings', 'tc_section_text', 'thatcamp_checks');
	//id attribute, displayed title, function to generate section, page we're putting it on, associated section
	add_settings_field('tc_syndicate_select', 'Set syndication', 'tc_settings_syndicate', 'thatcamp_checks', 'tc_main');
	add_settings_field('tc_accordion_select', 'Set accordion', 'tc_settings_accordion', 'thatcamp_checks', 'tc_main');
}

function tc_section_text() {
	echo '<p>Set available options for your THATCamp Plugin.</p>';

}

function tc_settings_syndicate() {

	$options = get_option('tc_options');
	//pull in previous selection to display. 
	$setvalue = $options['syndicate'];
	//If the user has selected yes, make sure it displays.
	if ($setvalue == "syndicate"){
		?>
			<input type="checkbox" id="tc_syndicate_select" name="tc_options[syndicate]" value="syndicate" checked /> Use Google's Syndication Meta Tag to indicate aggregation?
		<?php
	} else {
		?>
			<input type="checkbox" id="tc_syndicate_select" name="tc_options[syndicate]" value="syndicate" /> Use Google's Syndication Meta Tag to indicate aggregation?
		<?php
	
	}
	
}

function tc_settings_accordion() {

	$options = get_option('tc_options');
	//pull in previous selection to display. 
	$setvalue = $options['accordion'];
	//If the user has selected yes, make sure it displays.
	if ($setvalue == "yes"){
		?>
			<input type="checkbox" id="tc_accordion_select" name="tc_options[accordion]" value="yes" checked /> Hide OpenGraph with a jQuery Accordion?
		<?php
	} else {
		?>
			<input type="checkbox" id="tc_accordion_select" name="tc_options[accordion]" value="yes" /> Hide OpenGraph with a jQuery Accordion?
		<?php
	
	}
	
}

// validate our options. 
//This is to prevent hackers from inserting malicious code. 
function tc_options_validate($input) {
	$options = get_option('tc_options');
	
	$options['syndicate'] = trim($input['syndicate']);
	//die( preg_match( '!\w!i', $newinput['syndicate'] ) );
	if(!preg_match('/^[-_\w\/]+$/i', $options['syndicate'])) {
		$options['syndicate'] = '';
	}


	$options['accordion'] = trim($input['accordion']);
	if(!preg_match('/^[-_\w\/]+$/i', $options['accordion'])) {
		$options['accordion'] = '';
	}
	
	return $options;

}

?>