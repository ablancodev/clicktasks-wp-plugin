<?php
defined( 'ABSPATH' ) || exit;

class CT_Taxonomies {

    public static function init(): void {
        add_action( 'init', array( __CLASS__, 'register' ) );
    }

    public static function register(): void {
        register_taxonomy( 'ct_tag', 'ct_task', array(
            'label'             => __( 'Tags', 'clicktasks' ),
            'public'            => false,
            'show_ui'           => false,
            'show_in_rest'      => false,
            'hierarchical'      => false,
            'show_admin_column' => false,
        ) );
    }
}
