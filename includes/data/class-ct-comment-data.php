<?php
defined( 'ABSPATH' ) || exit;

class CT_Comment_Data {

    public static function create( array $data ): int|WP_Error {
        $task_id = absint( $data['task_id'] ?? 0 );
        $content = wp_kses_post( $data['content'] ?? '' );

        if ( ! $task_id ) {
            return new WP_Error( 'missing_task', __( 'La tarea es obligatoria.', 'clicktasks' ) );
        }
        if ( empty( trim( $content ) ) ) {
            return new WP_Error( 'missing_content', __( 'El contenido es obligatorio.', 'clicktasks' ) );
        }

        $user = wp_get_current_user();

        $comment_id = wp_insert_comment( array(
            'comment_post_ID' => $task_id,
            'comment_content' => $content,
            'comment_author'  => $user->display_name,
            'user_id'         => $user->ID,
            'comment_approved' => 1,
        ) );

        if ( ! $comment_id ) {
            return new WP_Error( 'insert_failed', __( 'No se pudo crear el comentario.', 'clicktasks' ) );
        }

        return $comment_id;
    }

    public static function delete( int $comment_id ): bool {
        return (bool) wp_delete_comment( $comment_id, true );
    }

    public static function get_by_task( int $task_id ): array {
        $comments = get_comments( array(
            'post_id' => $task_id,
            'orderby' => 'comment_date',
            'order'   => 'ASC',
            'status'  => 'approve',
        ) );

        return array_map( array( __CLASS__, 'format' ), $comments );
    }

    public static function format( WP_Comment $comment ): array {
        return array(
            'id'      => (int) $comment->comment_ID,
            'content' => $comment->comment_content,
            'author'  => array(
                'id'     => (int) $comment->user_id,
                'name'   => $comment->comment_author,
                'avatar' => get_avatar_url( $comment->user_id, array( 'size' => 32 ) ),
            ),
            'date'    => $comment->comment_date,
        );
    }
}
