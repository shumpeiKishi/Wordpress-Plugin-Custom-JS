<?php 
/*
Plugin Name: Custom JS
Plugin URI: http://shumpeikishi.com/
Description: This plugin creates a meta field to add JavaScript to  each post and page. (Original idea is from http://digwp.com/2010/02/custom-css-per-post/)
Author: Shumpei Kishi 
Version: 0.0
Author URI: http://shumpeikishi.com
License: CC0
*/

class CustomJs {
	function __construct () {
		add_action('admin_menu', array($this, 'custom_js_hooks'));
		add_action('save_post', array($this, 'save_custom_js'));
		add_action('wp_head', array($this, 'insert_custom_js'));
	}

	function custom_js_hooks() {
		// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		add_meta_box('custom_js', 'Custom JS', array($this, 'custom_js_input'), 'post', 'normal', 'high');
		add_meta_box('custom_js', 'Custom JS', array($this, 'custom_js_input'), 'page', 'normal', 'high');
	}

	function custom_js_input () { // Callback for add_meta_box();
		global $post;
		echo '<input type="hidden" name="custom_js_noncename" id="custom_js_noncename" value="'.wp_create_nonce('custom_js').'">'; // Avoid Cross Site Request Forgeries (using form without logging in from other sites);
		echo '<textarea name="custom_js" id="custom_js", rows="5", cols="30" style="width: 100%;">'. (get_post_meta($post->ID, '_custom_js', true) ? get_post_meta($post->ID, '_custom_js', true) : 'window.onload = function () {/*    Write JavaScript inside these brackets*/    }' ) .'</textarea>'; // get_post_meta($post_id, $key, $single); -> Gets value from custom field '_custom_js'	 	
	}

	function save_custom_js ($post_id) {
		if (!wp_verify_nonce($_POST['custom_js_noncename'], 'custom_js')) return $post_id; // if nonce was not valid, do nothing;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id; // if autosave is activated, do nothing;
		$custom_js = $_POST['custom_js'];
		update_post_meta($post_id, '_custom_js', $custom_js);
	}

	function insert_custom_js () {
		if(is_page() || is_single()) {
			if(have_posts()) : while (have_posts()) : the_post();
			echo '<script type="text/javascript">/* custom JS for each page by Custom JS plugin */' . get_post_meta(get_the_ID(), '_custom_js', true) . '</script>';
			endwhile; endif;
			rewind_posts();
		}
	}
}

$custom_js = new CustomJs();
?>