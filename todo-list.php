<?php /*
* Plugin Name:       Todo List
* Description:       Todo List test task
* Version:           1.0.0
* Author:            Sergiy Nikitenko
*/

//Preventing direct access to this file
if (!defined('ABSPATH')) exit;

//Files Include
require_once plugin_dir_path(__FILE__) . 'includes/Frontend.php';

class TodoPlugin {

    //Make sure only one instance of this class can be created
    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //Action on plugin uninstalling
    public function todolist_plugin_uninstall() {
        global $wpdb;
        // delete all data that plugin created
        $wpdb->query(
            "DELETE FROM $wpdb->usermeta WHERE meta_key = '_user_todo_list'"
        );
    }

    public function plugin_assets() {
        //Adding Assets to the Plugin
        wp_enqueue_style( 'todo-plugin-styles', plugins_url('todo-list/assets/style/main.css'));
        wp_enqueue_script( 'todo-plugin-scripts', plugins_url( 'todo-list/assets/js/main.js'), [ 'jquery' ], false, true);
    }

    public function ajaxjs() {
        echo '<script>var wp_js = ' . json_encode(['ajax_url' => admin_url('admin-ajax.php')]) . ';</script>';
    }

    private function todolist_plugin_init_frontend() {
        // Init additional class
        new Frontend();
    }

    private function __construct() {
        // Register Plugin Assets
        add_action( 'wp_enqueue_scripts', [ $this, 'plugin_assets' ], 9999999999 );
        $this->todolist_plugin_init_frontend();

        //Adding Ajax
        add_action('wp_footer', [$this, 'ajaxjs'], 1);

        //Plugin uninstalling hook
        register_uninstall_hook( __FILE__, 'todolist_plugin_uninstall' );
    }
}

TodoPlugin::instance();