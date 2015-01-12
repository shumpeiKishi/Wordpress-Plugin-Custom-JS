<?php 
/*
Plugin Name: Custom CSS
Plugin URI: http://shumpeikishi.com/
Description: This plugin creates a meta field to add CSS styles to  each post and page. (Original idea is from http://digwp.com/2010/02/custom-css-per-post/)
Author: Shumpei Kishi 
Version: 0.0
Author URI: http://shumpeikishi.com
License: CC0
*/

class CustomCss {
	function __construct() {
		add_action('admin_menu', array($this, 'custom_css_hooks')); // Use array for callback in OOP;
		add_action('save_post', array($this, 'save_custom_css'));
		add_action('wp_head', array($this, 'insert_custom_css'));

	}

	function custom_css_hooks() {
		// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		add_meta_box('custom_css', 'Custom CSS', array($this, 'custom_css_input'), 'post', 'normal', 'high');
		add_meta_box('custom_css', 'Custom CSS', array($this, 'custom_css_input'), 'page', 'normal', 'high');
	}

	function custom_css_input() {
		global $post;
		echo '<input type="hidden" name="custom_css_noncename" id="custom_css_noncename" value="'.wp_create_nonce('custom_css').'">'; // Avoid Cross Site Request Forgeries (using form without logging in from other sites);
		echo '<textarea name="custom_css" id="custom_css" rows="5" cols="30" style="width: 100%">' . get_post_meta($post->ID, '_custom_css', true) . '</textarea>'; // get_post_meta($post_id, $key, $single); -> Gets value from custom field '_custom_css'
	}

	function save_custom_css($post_id) {
		if (!wp_verify_nonce($_POST['custom_css_noncename'], 'custom_css')) return $post_id; // if nonce was not valid, do nothing;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id; // if autosave is activated, do nothing;
		$custom_css = $_POST['custom_css'];
		update_post_meta($post_id, '_custom_css', $custom_css);
	}

	function insert_custom_css () {
		if (is_page() || is_single()) {
			if(have_posts()) : while (have_posts()) : the_post();
			echo '<style type="text/css">' . get_post_meta(get_the_ID(), '_custom_css', true). '/n</style>';
			endwhile; endif;
			rewind_posts();
		}
	}
}

$customCss = new CustomCss();
?>