<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax_Workspace {

    public static function init(): void {
        add_action( 'wp_ajax_ct_create_workspace', array( __CLASS__, 'create' ) );
        add_action( 'wp_ajax_ct_update_workspace', array( __CLASS__, 'update' ) );
        add_action( 'wp_ajax_ct_delete_workspace', array( __CLASS__, 'delete' ) );
        add_action( 'wp_ajax_ct_get_workspaces',   array( __CLASS__, 'get_all' ) );
    }

    public static function create(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $result = CT_Workspace_Data::create( array(
            'title' => sanitize_text_field( $_POST['title'] ?? '' ),
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_Workspace_Data::get( $result ) );
    }

    public static function update(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $id     = absint( $_POST['id'] ?? 0 );
        $result = CT_Workspace_Data::update( $id, array(
            'title' => sanitize_text_field( $_POST['title'] ?? '' ),
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_Workspace_Data::get( $id ) );
    }

    public static function delete(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $id = absint( $_POST['id'] ?? 0 );
        CT_Workspace_Data::delete( $id );

        wp_send_json_success( array( 'deleted' => $id ) );
    }

    public static function get_all(): void {
        CT_Ajax::verify_request();
        wp_send_json_success( CT_Workspace_Data::get_all() );
    }
}
