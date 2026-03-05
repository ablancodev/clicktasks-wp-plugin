<?php
defined( 'ABSPATH' ) || exit;

class CT_Ajax_Task {

    public static function init(): void {
        add_action( 'wp_ajax_ct_create_task',  array( __CLASS__, 'create' ) );
        add_action( 'wp_ajax_ct_update_task',  array( __CLASS__, 'update' ) );
        add_action( 'wp_ajax_ct_delete_task',  array( __CLASS__, 'delete' ) );
        add_action( 'wp_ajax_ct_get_task',     array( __CLASS__, 'get' ) );
        add_action( 'wp_ajax_ct_get_tasks',    array( __CLASS__, 'get_by_list' ) );
        add_action( 'wp_ajax_ct_reorder_tasks', array( __CLASS__, 'reorder' ) );
    }

    public static function create(): void {
        CT_Ajax::verify_request( 'ct_manage_tasks' );

        $assigned = array();
        if ( ! empty( $_POST['assigned'] ) ) {
            $assigned = is_array( $_POST['assigned'] )
                ? $_POST['assigned']
                : json_decode( wp_unslash( $_POST['assigned'] ), true );
        }

        $tags = array();
        if ( ! empty( $_POST['tags'] ) ) {
            $tags = is_array( $_POST['tags'] )
                ? $_POST['tags']
                : array_map( 'trim', explode( ',', wp_unslash( $_POST['tags'] ) ) );
        }

        $result = CT_Task_Data::create( array(
            'title'       => sanitize_text_field( $_POST['title'] ?? '' ),
            'description' => wp_kses_post( $_POST['description'] ?? '' ),
            'list_id'     => absint( $_POST['list_id'] ?? 0 ),
            'priority'    => sanitize_text_field( $_POST['priority'] ?? 'normal' ),
            'status'      => sanitize_text_field( $_POST['status'] ?? 'To Do' ),
            'assigned'    => $assigned,
            'due_date'    => sanitize_text_field( $_POST['due_date'] ?? '' ),
            'tags'        => $tags,
        ) );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_Task_Data::get( $result ) );
    }

    public static function update(): void {
        CT_Ajax::verify_request( 'ct_manage_tasks' );

        $id   = absint( $_POST['id'] ?? 0 );
        $data = array();

        if ( isset( $_POST['title'] ) ) {
            $data['title'] = sanitize_text_field( $_POST['title'] );
        }
        if ( isset( $_POST['description'] ) ) {
            $data['description'] = wp_kses_post( $_POST['description'] );
        }
        if ( isset( $_POST['priority'] ) ) {
            $data['priority'] = sanitize_text_field( $_POST['priority'] );
        }
        if ( isset( $_POST['status'] ) ) {
            $data['status'] = sanitize_text_field( $_POST['status'] );
        }
        if ( isset( $_POST['assigned'] ) ) {
            $data['assigned'] = is_array( $_POST['assigned'] )
                ? $_POST['assigned']
                : json_decode( wp_unslash( $_POST['assigned'] ), true );
        }
        if ( array_key_exists( 'due_date', $_POST ) ) {
            $data['due_date'] = sanitize_text_field( $_POST['due_date'] );
        }
        if ( isset( $_POST['tags'] ) ) {
            $data['tags'] = is_array( $_POST['tags'] )
                ? $_POST['tags']
                : array_map( 'trim', explode( ',', wp_unslash( $_POST['tags'] ) ) );
        }

        $result = CT_Task_Data::update( $id, $data );

        if ( is_wp_error( $result ) ) {
            CT_Ajax::send_error( $result );
        }

        wp_send_json_success( CT_Task_Data::get( $id ) );
    }

    public static function delete(): void {
        CT_Ajax::verify_request( 'ct_manage_tasks' );

        $id = absint( $_POST['id'] ?? 0 );
        CT_Task_Data::delete( $id );

        wp_send_json_success( array( 'deleted' => $id ) );
    }

    public static function get(): void {
        CT_Ajax::verify_request();

        $id   = absint( $_POST['id'] ?? 0 );
        $task = CT_Task_Data::get( $id );

        if ( ! $task ) {
            CT_Ajax::send_error( __( 'Tarea no encontrada.', 'clicktasks' ) );
        }

        wp_send_json_success( $task );
    }

    public static function get_by_list(): void {
        CT_Ajax::verify_request();

        $list_id = absint( $_POST['list_id'] ?? 0 );
        $filters = array();

        if ( ! empty( $_POST['filter_status'] ) ) {
            $filters['status'] = sanitize_text_field( $_POST['filter_status'] );
        }
        if ( ! empty( $_POST['filter_priority'] ) ) {
            $filters['priority'] = sanitize_text_field( $_POST['filter_priority'] );
        }
        if ( ! empty( $_POST['filter_assigned'] ) ) {
            $filters['assigned'] = absint( $_POST['filter_assigned'] );
        }

        $tasks = CT_Task_Data::get_by_list( $list_id, $filters, get_current_user_id() );

        wp_send_json_success( $tasks );
    }

    public static function reorder(): void {
        CT_Ajax::verify_request( 'ct_manage_tasks' );

        $items = json_decode( wp_unslash( $_POST['items'] ?? '[]' ), true );
        if ( ! is_array( $items ) ) {
            CT_Ajax::send_error( __( 'Datos inválidos.', 'clicktasks' ) );
        }

        CT_Task_Data::reorder( $items );

        wp_send_json_success( array( 'reordered' => true ) );
    }
}
