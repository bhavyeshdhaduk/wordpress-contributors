<?php
/**
 *
 * WordPress Contributors plugin.
 *
 * @link              https://github.com/bhavyeshdhaduk/wordpress-contributors
 * @since             1.0.0
 * @package           Wordpress_Contributors
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Contributors
 * Plugin URI:        https://github.com/bhavyeshdhaduk/wordpress-contributors
 * Description:       This plugin allows you to display Contributors in the post.
 * Version:           1.0.0
 * Author:            bhavyesh dhaduk
 * Author URI:        https://github.com/bhavyeshdhaduk
 * Text Domain:       wordpress-contributors
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Current plugin version and name.
define( 'WORDPRESS_CONTRIBUTER_VERSION', '1.0.0' );
define( 'WORDPRESS_PLUGIN_NAME', 'wordpress-contributors' );

// The class responsible for defining all actions that occur in the admin area.
require_once plugin_dir_path( __FILE__ ) . 'admin/class-wordpress-contributors-admin.php';

// The class responsible for defining all actions that occur in the public-facing.
require_once plugin_dir_path( __FILE__ ) . 'public/class-wordpress-contributors-public.php';

/**
 * Begins execution of the plugin.
 */
function run_wordpress_contributors() {
	$plugin_admin  = new WordPress_Contributors_Admin( WORDPRESS_PLUGIN_NAME, WORDPRESS_CONTRIBUTER_VERSION );
	$plugin_public = new WordPress_Contributors_Public( WORDPRESS_PLUGIN_NAME, WORDPRESS_CONTRIBUTER_VERSION );
}
run_wordpress_contributors();
