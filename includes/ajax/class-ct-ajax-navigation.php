<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax_Navigation {

    public static function init(): void {
        add_action( 'wp_ajax_ct_get_navigation', array( __CLASS__, 'get_tree' ) );
        add_action( 'wp_ajax_ct_get_users',      array( __CLASS__, 'get_users' ) );
    }

    public static function get_tree(): void {
        CT_Ajax::verify_request();

        $workspaces = CT_Workspace_Data::get_all();
        $tree       = array();

        foreach ( $workspaces as $ws ) {
            $ws['folders'] = array();
            $folders = CT_Folder_Data::get_by_workspace( $ws['id'] );

            foreach ( $folders as $folder ) {
                $folder['lists'] = CT_List_Data::get_by_folder( $folder['id'] );
                $ws['folders'][] = $folder;
            }

            $tree[] = $ws;
        }

        wp_send_json_success( $tree );
    }

    public static function get_users(): void {
        CT_Ajax::verify_request();

        $users = get_users( array(
            'role__in' => array( 'administrator', 'editor', 'author' ),
            'fields'   => array( 'ID', 'display_name', 'user_email' ),
        ) );

        $result = array();
        foreach ( $users as $user ) {
            $result[] = array(
                'id'     => (int) $user->ID,
                'name'   => $user->display_name,
                'avatar' => get_avatar_url( $user->ID, array( 'size' => 32 ) ),
            );
        }

        wp_send_json_success( $result );
    }
}
