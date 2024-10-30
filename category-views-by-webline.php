<?php
/*
 * Plugin Name: Category Image and Views by Webline
 * Plugin URI: http://www.weblineindia.com/
 * Description: A Simple plugin allow you to upload category image and have categories displayed in widget using different display options.
 * Version: 1.0.7
 * Author: Weblineindia
 * Author URI: http://www.weblineindia.com/
 * License: GPL
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );

define( 'CV_VERSION', $plugin_data['Version'] );
define( 'CV_DEBUG', TRUE );
define( 'CV_PATH', plugin_dir_path( __FILE__ ) );
define( 'CV_URL', plugins_url( '', __FILE__ ) );
define( 'CV_PLUGIN_FILE', basename( __FILE__ ) );
define( 'CV_PLUGIN_DIR', plugin_basename( dirname( __FILE__ ) ) );
define( 'CV_ADMIN_DIR', CV_PATH . '/admin' );
define( 'CV_PUBLIC_DIR', 'public' );
define( 'CV_PUBLIC', CV_PATH . CV_PUBLIC_DIR );
define( 'CV_DEFAULT_IMAGE', CV_URL . '/public/assets/images/default.jpg' );

// Adding Hook Class
require_once ( CV_PUBLIC . '/hook.php' );

require_once ( CV_PUBLIC . '/category-views.php' );

?>
