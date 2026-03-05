<?php
defined( 'ABSPATH' ) || exit;

class CT_Loader {

    private static ?CT_Loader $instance = null;

    public static function instance(): CT_Loader {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        CT_Post_Types::init();
        CT_Taxonomies::init();
        CT_Shortcode::init();
        CT_Assets::init();
        CT_Ajax::init();
        CT_Comments::init();

        load_plugin_textdomain( 'clicktasks', false, dirname( CT_PLUGIN_BASENAME ) . '/languages' );
    }
}
