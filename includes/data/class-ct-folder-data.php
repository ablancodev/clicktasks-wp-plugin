<?php
defined( 'ABSPATH' ) || exit;

class CT_Folder_Data {

    public static function create( array $data ): int|WP_Error {
        $title        = sanitize_text_field( $data['title'] ?? '' );
        $workspace_id = absint( $data['workspace_id'] ?? 0 );

        if ( empty( $title ) ) {
            return new WP_Error( 'missing_title', __( 'El título es obligatorio.', 'clicktasks' ) );
        }
        if ( ! $workspace_id ) {
            return new WP_Error( 'missing_workspace', __( 'El workspace es obligatorio.', 'clicktasks' ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'ct_folder',
            'post_title'  => $title,
            'post_status' => 'publish',
        ), true );

        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_ct_workspace_id', $workspace_id );
        }

        return $post_id;
    }

    public static function update( int $id, array $data ): int|WP_Error {
        $args = array( 'ID' => $id );

        if ( isset( $data['title'] ) ) {
            $args['post_title'] = sanitize_text_field( $data['title'] );
        }

        return wp_update_post( $args, true );
    }

    public static function delete( int $id ): bool {
        $lists = CT_List_Data::get_by_folder( $id );
        foreach ( $lists as $list ) {
            CT_List_Data::delete( $list['id'] );
        }
        return (bool) wp_delete_post( $id, true );
    }

    public static function get( int $id ): ?array {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'ct_folder' ) {
            return null;
        }
        return self::format( $post );
    }

    public static function get_by_workspace( int $workspace_id ): array {
        $posts = get_posts( array(
            'post_type'      => 'ct_folder',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_ct_workspace_id',
                    'value' => $workspace_id,
                    'type'  => 'NUMERIC',
                ),
            ),
        ) );

        return array_map( array( __CLASS__, 'format' ), $posts );
    }

    public static function format( WP_Post $post ): array {
        return array(
            'id'           => $post->ID,
            'title'        => $post->post_title,
            'workspace_id' => (int) get_post_meta( $post->ID, '_ct_workspace_id', true ),
        );
    }
}
