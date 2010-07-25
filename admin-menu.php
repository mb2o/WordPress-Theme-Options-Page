<?php

add_action('admin_menu', 'create_theme_options_page');
add_action('admin_init', 'register_and_build_fields' );

function create_theme_options_page() {
	add_options_page('Theme Options', 'Theme Options', 'administrator', __FILE__, 'options_page_fn');
}


function register_and_build_fields(){
	register_setting( 'plugin_options', 'plugin_options', 'validate_setting' );
	
	add_settings_section('main_section', 'Main Settings', 'section_text_fn', __FILE__);
	
	add_settings_field('color_scheme', 'Color Scheme:', 'color_scheme_setting', __FILE__, 'main_section');
	add_settings_field('logo', 'Logo:', 'logo_setting', __FILE__, 'main_section'); // LOGO
	add_settings_field('banner_heading', 'Banner Heading:', 'banner_heading_setting', __FILE__, 'main_section');
	add_settings_field('adverting_information', 'Advertising Info:', 'advertising_information_setting', __FILE__, 'main_section');
	
	add_settings_field('ad_one', 'Ad:', 'ad_setting_one', __FILE__, 'main_section'); // Ad1
	add_settings_field('ad_two', 'Second Ad:', 'ad_setting_two', __FILE__, 'main_section'); // Ad2
	
	/*	
	add_settings_field('plugin_chk2', 'A Checkbox', 'setting_chk2_fn', __FILE__, 'main_section');
	*/
}


function options_page_fn() {
?>
	<div id="theme-options-wrap" class="widefat">
		<div class="icon32" id="icon-tools"></div>
		
		<h2>My Theme Options</h2>
		<p>Take control of your theme, by overriding the default settings with your own specific preferences.</p>
		
		<form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields('plugin_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
		</form>
	</div>
<?php
}


// Banner Heading
function banner_heading_setting() {
	$options = get_option('plugin_options');
	echo "<input name='plugin_options[banner_heading]' type='text' value='{$options['banner_heading']}' />";
}


// Color Scheme
function  color_scheme_setting() {
	$options = get_option('plugin_options');
	$items = array("Red", "Green", "Blue");
	
	echo "<select name='plugin_options[color_scheme]'>";
		foreach( $items as $item ) {
			$selected = ( $options['color_scheme'] === $item ) ? 'selected = "selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
	echo "</select>";
}


// Advertising info
function advertising_information_setting() {
	$options = get_option('plugin_options');
	echo "<textarea name='plugin_options[advertising_information]' rows='10' cols='60' type='textarea'>{$options['advertising_information']}</textarea>";
}


// Ad one
function ad_setting_one() {
	echo '<input type="file" name="ad_one" />'; 
}


// Ad two
function ad_setting_two() {
	echo '<input type="file" name="ad_two" />'; 
}


// Logo
function logo_setting() {
	echo '<input type="file" name="logo" />';
}


function validate_setting($plugin_options) {
	$keys = array_keys($_FILES);
	$i = 0;
	
	foreach( $_FILES as $image ) {
		// if a files was upload
		if ( $image['size'] ) {
			// if it is an image
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {
		       	$override = array('test_form' => false); 
		       	$file = wp_handle_upload($image, $override);

				$plugin_options[$keys[$i]] = $file['url'];
			}
			else {
				$options = get_option('plugin_options');
				$plugin_options[$keys[$i]] = $options[$logo];
				wp_die('No image was uploaded.');
			}
		 }
	
		// else, retain the image that's already on file.
		else {
			$options = get_option('plugin_options');
			$plugin_options[$keys[$i]] = $options[$keys[$i]];
		}
		$i++;
	}

	return $plugin_options;
}
	

function section_text_fn() {}


// Add stylesheet
add_action('admin_head', 'admin_register_head');
function admin_register_head() {
    $url = get_bloginfo('template_directory') . '/functions/options_page.css';
    echo "<link rel='stylesheet' href='$url' />\n";
}

/*
register_activation_hook(__FILE__, 'add_defaults');

function add_defaults() {
	$arr = array("dropdown1"=>"Orange", "text_area" => "Space to put a lot of information here!", "text_string" => "Some sample text", "pass_string" => "123456", "chkbox1" => "", "chkbox2" => "on", "option_set1" => "Triangle");
	    update_option('plugin_options', $arr);
}
*/