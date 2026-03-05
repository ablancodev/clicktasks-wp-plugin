<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax_Folder {

    public static function init(): void {
        add_action( 'wp_ajax_ct_create_folder', array( __CLASS__, 'create' ) );
        add_action( 'wp_ajax_ct_update_folder', array( __CLASS__, 'update' ) );
        add_action( 'wp_ajax_ct_delete_folder', array( __CLASS__, 'delete' ) );
    }

    public static function create(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $result = CT_Folder_Data::create( array(
            'title'        => sanitize_text_field( $_POST['title'] ?? '' ),
            'workspace_id' => absint( $_POST['workspace_id'] ?? 0 ),
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_Folder_Data::get( $result ) );
    }

    public static function update(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $id     = absint( $_POST['id'] ?? 0 );
        $result = CT_Folder_Data::update( $id, array(
            'title' => sanitize_text_field( $_POST['title'] ?? '' ),
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_Folder_Data::get( $id ) );
    }

    public static function delete(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $id = absint( $_POST['id'] ?? 0 );
        CT_Folder_Data::delete( $id );

        wp_send_json_success( array( 'deleted' => $id ) );
    }
}
