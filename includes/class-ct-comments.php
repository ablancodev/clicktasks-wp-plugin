<?php
defined( 'ABSPATH' ) || exit;

class CT_Comments {

    public static function init(): void {
        add_filter( 'comments_open', array( __CLASS__, 'enable_comments' ), 10, 2 );
        add_filter( 'get_default_comment_status', array( __CLASS__, 'default_status' ), 10, 3 );
    }

    public static function enable_comments( bool $open, int $post_id ): bool {
        if ( get_post_type( $post_id ) === 'ct_task' ) {
            return true;
        }
        return $open;
    }

    public static function default_status( string $status, string $post_type, string $comment_type ): string {
        if ( $post_type === 'ct_task' ) {
            return 'open';
        }
        return $status;
    }
}
