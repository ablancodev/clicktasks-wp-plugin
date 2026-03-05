<?php
defined( 'ABSPATH' ) || exit;

class CT_Post_Types {

    public static function init(): void {
        add_action( 'init', array( __CLASS__, 'register' ) );
        add_filter( 'map_meta_cap', array( __CLASS__, 'map_meta_cap' ), 10, 4 );
    }

    public static function register(): void {
        $shared = array(
            'public'             => false,
            'show_ui'            => false,
            'show_in_rest'       => false,
            'capability_type'    => array( 'ct_item', 'ct_items' ),
            'map_meta_cap'       => true,
            'supports'           => array( 'title' ),
        );

        register_post_type( 'ct_workspace', array_merge( $shared, array(
            'label' => __( 'Workspaces', 'clicktasks' ),
        ) ) );

        register_post_type( 'ct_folder', array_merge( $shared, array(
            'label' => __( 'Folders', 'clicktasks' ),
        ) ) );

        register_post_type( 'ct_list', array_merge( $shared, array(
            'label' => __( 'Lists', 'clicktasks' ),
        ) ) );

        register_post_type( 'ct_task', array_merge( $shared, array(
            'label'    => __( 'Tasks', 'clicktasks' ),
            'supports' => array( 'title', 'editor', 'comments' ),
        ) ) );
    }

    public static function map_meta_cap( array $caps, string $cap, int $user_id, array $args ): array {
        $meta_caps = array(
            'edit_ct_item',
            'read_ct_item',
            'delete_ct_item',
        );

        if ( ! in_array( $cap, $meta_caps, true ) ) {
            return $caps;
        }

        $post = get_post( $args[0] ?? 0 );
        if ( ! $post ) {
            return array( 'do_not_allow' );
        }

        switch ( $cap ) {
            case 'edit_ct_item':
                if ( (int) $post->post_author === $user_id ) {
                    $caps = array( 'edit_ct_items' );
                } else {
                    $caps = array( 'edit_others_ct_items' );
                }
                break;
            case 'read_ct_item':
                $caps = array( 'read' );
                break;
            case 'delete_ct_item':
                if ( (int) $post->post_author === $user_id ) {
                    $caps = array( 'delete_ct_items' );
                } else {
                    $caps = array( 'delete_others_ct_items' );
                }
                break;
        }

        return $caps;
    }
}
