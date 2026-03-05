<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax_Comment {

    public static function init(): void {
        add_action( 'wp_ajax_ct_create_comment',  array( __CLASS__, 'create' ) );
        add_action( 'wp_ajax_ct_delete_comment',  array( __CLASS__, 'delete' ) );
        add_action( 'wp_ajax_ct_get_comments',    array( __CLASS__, 'get_by_task' ) );
    }

    public static function create(): void {
        CT_Ajax::verify_request();

        $result = CT_Comment_Data::create( array(
            'task_id' => absint( $_POST['task_id'] ?? 0 ),
            'content' => wp_kses_post( $_POST['content'] ?? '' ),
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        $comments = CT_Comment_Data::get_by_task( absint( $_POST['task_id'] ) );
        wp_send_json_success( $comments );
    }

    public static function delete(): void {
        CT_Ajax::verify_request( 'ct_manage_tasks' );

        $id = absint( $_POST['id'] ?? 0 );
        CT_Comment_Data::delete( $id );

        wp_send_json_success( array( 'deleted' => $id ) );
    }

    public static function get_by_task(): void {
        CT_Ajax::verify_request();

        $task_id  = absint( $_POST['task_id'] ?? 0 );
        $comments = CT_Comment_Data::get_by_task( $task_id );

        wp_send_json_success( $comments );
    }
}
