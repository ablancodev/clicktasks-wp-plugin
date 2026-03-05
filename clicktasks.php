<?php
/**
 * Plugin Name: ClickTasks
 * Description: Gestor de tareas para WordPress.
 * Version: 1.0.0
 * Author: ablancodev
 * Author URI: https://ablancodev.com
 * Text Domain: clicktasks
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

defined( 'ABSPATH' ) || exit;

define( 'CT_VERSION', '1.0.0' );
define( 'CT_PLUGIN_FILE', __FILE__ );
define( 'CT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/* Autoload includes */
require_once CT_PLUGIN_DIR . 'includes/class-ct-loader.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-post-types.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-taxonomies.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-roles.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-shortcode.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-assets.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-ajax.php';
require_once CT_PLUGIN_DIR . 'includes/class-ct-comments.php';

/* Data layer */
require_once CT_PLUGIN_DIR . 'includes/data/class-ct-workspace-data.php';
require_once CT_PLUGIN_DIR . 'includes/data/class-ct-folder-data.php';
require_once CT_PLUGIN_DIR . 'includes/data/class-ct-list-data.php';
require_once CT_PLUGIN_DIR . 'includes/data/class-ct-task-data.php';
require_once CT_PLUGIN_DIR . 'includes/data/class-ct-comment-data.php';

/* AJAX handlers */
require_once CT_PLUGIN_DIR . 'includes/ajax/class-ct-ajax-workspace.php';
require_once CT_PLUGIN_DIR . 'includes/ajax/class-ct-ajax-folder.php';
require_once CT_PLUGIN_DIR . 'includes/ajax/class-ct-ajax-list.php';
require_once CT_PLUGIN_DIR . 'includes/ajax/class-ct-ajax-task.php';
require_once CT_PLUGIN_DIR . 'includes/ajax/class-ct-ajax-comment.php';
require_once CT_PLUGIN_DIR . 'includes/ajax/class-ct-ajax-navigation.php';

/* Activation / Deactivation */
register_activation_hook( __FILE__, array( 'CT_Roles', 'activate' ) );
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, array( 'CT_Roles', 'deactivate' ) );

/* Boot */
add_action( 'plugins_loaded', array( 'CT_Loader', 'instance' ) );
