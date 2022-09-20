<?php
/**
 * Plugin Name: Leadgenpro API
 * Description: Leadgenpro API is for managing wordpress sites connected with leadgenpro.ca CRM to generate dynamic pages and form. This plugin enables basic authentication for rest API and loads custom templates for pages cretated by leadgenpro.
 * Author: Leadgenpro - INDigital Group
 * Author URI: https://indigitalgroup.ca
 * Version: 1.1
 * Plugin URI: https://github.com/rrRRohith/wp-leadgenpro
 */

/**
* Filter the single_template|singular_template with our custom function
*
**/
add_filter('single_template', 'leadgen_template', 999);
add_filter('singular_template', 'leadgen_template', 999);
add_filter('template_include', 'leadgen_template', 999);

function leadgen_template($single) {
    global $post;
    
    if($post->post_type == 'page' && metadata_exists('post', $post->ID, 'leadgen')){
        if(file_exists(plugin_dir_path(__FILE__).'page.php')){
            return plugin_dir_path(__FILE__).'page.php';
        }
    }
    return $single;
}

/**
 * Disable default the_content formatting if the post created by leadgen API.
 * 
 */
add_filter( 'the_content', 'wti_remove_autop_for_image', 0 );
function wti_remove_autop_for_image( $content ){
     global $post;
     
     if($post->post_type == 'page' && metadata_exists('post', $post->ID, 'leadgen'))
        remove_filter('the_content', 'wpautop');
          
     return $content;
}
function json_basic_auth_handler($user){
	global $wp_json_basic_auth_error;

	$wp_json_basic_auth_error = null;

	// Don't authenticate twice
	if(!empty( $user)){

		return $user;

	}

	// Check that we're trying to authenticate
	if(!isset( $_SERVER['PHP_AUTH_USER'])){

		return $user;

	}

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];

	/**
	 * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
	 * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
	 * recursion and a stack overflow unless the current function is removed from the determine_current_user
	 * filter during authentication.
	 **/
	remove_filter('determine_current_user', 'json_basic_auth_handler', 20);

	$user = wp_authenticate($username, $password);

	add_filter('determine_current_user', 'json_basic_auth_handler', 20);

	if(is_wp_error($user)){

		$wp_json_basic_auth_error = $user;
		return null;

	}

	$wp_json_basic_auth_error = true;

	return $user->ID;
}
add_filter('determine_current_user', 'json_basic_auth_handler', 20);

/**
 * Handle creating posts with custom meta fields.
 * 
 **/
add_action("rest_insert_page", function (\WP_Post $post, $request, $creating) {
    $metas = $request->get_param("meta");
    if (is_array($metas)) {
        foreach ($metas as $name => $value) {
            update_post_meta($post->ID, $name, $value);
        }
    }
}, 10, 3);
