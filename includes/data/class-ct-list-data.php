<?php
defined( 'ABSPATH' ) || exit;

class CT_List_Data {

    public static $default_statuses = array(
        array( 'name' => 'To Do',        'color' => '#9CA3AF' ),
        array( 'name' => 'In Progress',  'color' => '#3B82F6' ),
        array( 'name' => 'In Review',    'color' => '#F59E0B' ),
        array( 'name' => 'Done',         'color' => '#10B981' ),
    );

    public static function create( array $data ): int|WP_Error {
        $title     = sanitize_text_field( $data['title'] ?? '' );
        $folder_id = absint( $data['folder_id'] ?? 0 );

        if ( empty( $title ) ) {
            return new WP_Error( 'missing_title', __( 'El título es obligatorio.', 'clicktasks' ) );
        }
        if ( ! $folder_id ) {
            return new WP_Error( 'missing_folder', __( 'La carpeta es obligatoria.', 'clicktasks' ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'ct_list',
            'post_title'  => $title,
            'post_status' => 'publish',
        ), true );

        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_ct_folder_id', $folder_id );
            update_post_meta( $post_id, '_ct_statuses', self::$default_statuses );
        }

        return $post_id;
    }

    public static function update( int $id, array $data ): int|WP_Error {
        $args = array( 'ID' => $id );

        if ( isset( $data['title'] ) ) {
            $args['post_title'] = sanitize_text_field( $data['title'] );
        }

        $result = wp_update_post( $args, true );

        if ( ! is_wp_error( $result ) && isset( $data['statuses'] ) && is_array( $data['statuses'] ) ) {
            $statuses = array_map( function( $s ) {
                return array(
                    'name'  => sanitize_text_field( $s['name'] ?? '' ),
                    'color' => sanitize_hex_color( $s['color'] ?? '#D3D3D3' ) ?: '#D3D3D3',
                );
            }, $data['statuses'] );
            update_post_meta( $id, '_ct_statuses', $statuses );
        }

        return $result;
    }

    public static function delete( int $id ): bool {
        $tasks = CT_Task_Data::get_by_list( $id );
        foreach ( $tasks as $task ) {
            CT_Task_Data::delete( $task['id'] );
        }
        return (bool) wp_delete_post( $id, true );
    }

    public static function get( int $id ): ?array {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'ct_list' ) {
            return null;
        }
        return self::format( $post );
    }

    public static function get_by_folder( int $folder_id ): array {
        $posts = get_posts( array(
            'post_type'      => 'ct_list',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_ct_folder_id',
                    'value' => $folder_id,
                    'type'  => 'NUMERIC',
                ),
            ),
        ) );

        return array_map( array( __CLASS__, 'format' ), $posts );
    }

    public static function format( WP_Post $post ): array {
        $statuses = get_post_meta( $post->ID, '_ct_statuses', true );
        if ( ! is_array( $statuses ) || empty( $statuses ) ) {
            $statuses = self::$default_statuses;
        }

        return array(
            'id'        => $post->ID,
            'title'     => $post->post_title,
            'folder_id' => (int) get_post_meta( $post->ID, '_ct_folder_id', true ),
            'statuses'  => $statuses,
        );
    }
}
