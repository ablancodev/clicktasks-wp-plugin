<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax_List {

    public static function init(): void {
        add_action( 'wp_ajax_ct_create_list', array( __CLASS__, 'create' ) );
        add_action( 'wp_ajax_ct_update_list', array( __CLASS__, 'update' ) );
        add_action( 'wp_ajax_ct_delete_list', array( __CLASS__, 'delete' ) );
        add_action( 'wp_ajax_ct_get_list',    array( __CLASS__, 'get' ) );
    }

    public static function create(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $result = CT_List_Data::create( array(
            'title'     => sanitize_text_field( $_POST['title'] ?? '' ),
            'folder_id' => absint( $_POST['folder_id'] ?? 0 ),
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_List_Data::get( $result ) );
    }

    public static function update(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $id   = absint( $_POST['id'] ?? 0 );
        $data = array( 'title' => sanitize_text_field( $_POST['title'] ?? '' ) );

        if ( ! empty( $_POST['statuses'] ) ) {
            $data['statuses'] = json_decode( wp_unslash( $_POST['statuses'] ), true );
        }

        $result = CT_List_Data::update( $id, $data );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_List_Data::get( $id ) );
    }

    public static function delete(): void {
        CT_Ajax::verify_request( 'ct_manage_workspaces' );

        $id = absint( $_POST['id'] ?? 0 );
        CT_List_Data::delete( $id );

        wp_send_json_success( array( 'deleted' => $id ) );
    }

    public static function get(): void {
        CT_Ajax::verify_request();

        $id   = absint( $_POST['id'] ?? 0 );
        $list = CT_List_Data::get( $id );

        if ( ! $list ) {
            CT_Ajax::send_error( __( 'Lista no encontrada.', 'clicktasks' ) );
        }

        wp_send_json_success( $list );
    }
}
