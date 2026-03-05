<?php
defined( 'ABSPATH' ) || exit;

class CT_Workspace_Data {

    public static function create( array $data ): int|WP_Error {
        $title = sanitize_text_field( $data['title'] ?? '' );
        if ( empty( $title ) ) {
            return new WP_Error( 'missing_title', __( 'El título es obligatorio.', 'clicktasks' ) );
        }

        return wp_insert_post( array(
            'post_type'   => 'ct_workspace',
            'post_title'  => $title,
            'post_status' => 'publish',
        ), true );
    }

    public static function update( int $id, array $data ): int|WP_Error {
        $args = array( 'ID' => $id );

        if ( isset( $data['title'] ) ) {
            $args['post_title'] = sanitize_text_field( $data['title'] );
        }

        return wp_update_post( $args, true );
    }

    public static function delete( int $id ): bool {
        $folders = CT_Folder_Data::get_by_workspace( $id );
        foreach ( $folders as $folder ) {
            CT_Folder_Data::delete( $folder['id'] );
        }
        return (bool) wp_delete_post( $id, true );
    }

    public static function get( int $id ): ?array {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'ct_workspace' ) {
            return null;
        }
        return self::format( $post );
    }

    public static function get_all(): array {
        $posts = get_posts( array(
            'post_type'      => 'ct_workspace',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
        ) );

        return array_map( array( __CLASS__, 'format' ), $posts );
    }

    public static function format( WP_Post $post ): array {
        return array(
            'id'    => $post->ID,
            'title' => $post->post_title,
        );
    }
}
