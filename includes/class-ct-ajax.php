<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax {

    public static function init(): void {
        CT_Ajax_Workspace::init();
        CT_Ajax_Folder::init();
        CT_Ajax_List::init();
        CT_Ajax_Task::init();
        CT_Ajax_Comment::init();
        CT_Ajax_Navigation::init();
    }

    public static function verify_request( string $capability = 'ct_access_app' ): void {
        if ( ! check_ajax_referer( 'ct_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Nonce inválido.', 'clicktasks' ) ), 403 );
        }
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error( array( 'message' => __( 'No tienes permisos.', 'clicktasks' ) ), 403 );
        }
    }

    public static function send_error( WP_Error|string $error ): void {
        $message = is_wp_error( $error ) ? $error->get_error_message() : $error;
        wp_send_json_error( array( 'message' => $message ) );
    }
}
