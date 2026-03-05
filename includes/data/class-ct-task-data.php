<?php
defined( 'ABSPATH' ) || exit;

class CT_Task_Data {

    public static array $priorities = array( 'urgent', 'high', 'normal', 'low' );

    public static function create( array $data ): int|WP_Error {
        $title   = sanitize_text_field( $data['title'] ?? '' );
        $list_id = absint( $data['list_id'] ?? 0 );

        if ( empty( $title ) ) {
            return new WP_Error( 'missing_title', __( 'El título es obligatorio.', 'clicktasks' ) );
        }
        if ( ! $list_id ) {
            return new WP_Error( 'missing_list', __( 'La lista es obligatoria.', 'clicktasks' ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'    => 'ct_task',
            'post_title'   => $title,
            'post_content' => wp_kses_post( $data['description'] ?? '' ),
            'post_status'  => 'publish',
        ), true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, '_ct_list_id', $list_id );

        $priority = sanitize_text_field( $data['priority'] ?? 'normal' );
        if ( ! in_array( $priority, self::$priorities, true ) ) {
            $priority = 'normal';
        }
        update_post_meta( $post_id, '_ct_priority', $priority );

        $status = sanitize_text_field( $data['status'] ?? 'To Do' );
        update_post_meta( $post_id, '_ct_status', $status );

        $assigned = array_map( 'absint', (array) ( $data['assigned'] ?? array() ) );
        update_post_meta( $post_id, '_ct_assigned', $assigned );

        if ( ! empty( $data['due_date'] ) ) {
            update_post_meta( $post_id, '_ct_due_date', sanitize_text_field( $data['due_date'] ) );
        }

        $position = self::get_next_position( $list_id );
        update_post_meta( $post_id, '_ct_position', $position );

        if ( ! empty( $data['tags'] ) ) {
            $tags = array_map( 'sanitize_text_field', (array) $data['tags'] );
            wp_set_object_terms( $post_id, $tags, 'ct_tag' );
        }

        return $post_id;
    }

    public static function update( int $id, array $data ): int|WP_Error {
        $args = array( 'ID' => $id );

        if ( isset( $data['title'] ) ) {
            $args['post_title'] = sanitize_text_field( $data['title'] );
        }
        if ( isset( $data['description'] ) ) {
            $args['post_content'] = wp_kses_post( $data['description'] );
        }

        $result = wp_update_post( $args, true );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        if ( isset( $data['priority'] ) ) {
            $priority = sanitize_text_field( $data['priority'] );
            if ( in_array( $priority, self::$priorities, true ) ) {
                update_post_meta( $id, '_ct_priority', $priority );
            }
        }
        if ( isset( $data['status'] ) ) {
            update_post_meta( $id, '_ct_status', sanitize_text_field( $data['status'] ) );
        }
        if ( isset( $data['assigned'] ) ) {
            $assigned = array_map( 'absint', (array) $data['assigned'] );
            update_post_meta( $id, '_ct_assigned', $assigned );
        }
        if ( array_key_exists( 'due_date', $data ) ) {
            if ( empty( $data['due_date'] ) ) {
                delete_post_meta( $id, '_ct_due_date' );
            } else {
                update_post_meta( $id, '_ct_due_date', sanitize_text_field( $data['due_date'] ) );
            }
        }
        if ( isset( $data['position'] ) ) {
            update_post_meta( $id, '_ct_position', absint( $data['position'] ) );
        }
        if ( isset( $data['tags'] ) ) {
            $tags = array_map( 'sanitize_text_field', (array) $data['tags'] );
            wp_set_object_terms( $id, $tags, 'ct_tag' );
        }

        return $result;
    }

    public static function delete( int $id ): bool {
        return (bool) wp_delete_post( $id, true );
    }

    public static function get( int $id ): ?array {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'ct_task' ) {
            return null;
        }
        return self::format( $post );
    }

    public static function get_by_list( int $list_id, array $filters = array(), ?int $user_id = null ): array {
        $meta_query = array(
            array(
                'key'   => '_ct_list_id',
                'value' => $list_id,
                'type'  => 'NUMERIC',
            ),
        );

        if ( ! empty( $filters['status'] ) ) {
            $meta_query[] = array(
                'key'   => '_ct_status',
                'value' => sanitize_text_field( $filters['status'] ),
            );
        }
        if ( ! empty( $filters['priority'] ) ) {
            $meta_query[] = array(
                'key'   => '_ct_priority',
                'value' => sanitize_text_field( $filters['priority'] ),
            );
        }

        $args = array(
            'post_type'      => 'ct_task',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => $meta_query,
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_ct_position',
            'order'          => 'ASC',
        );

        /* Author role: only show assigned tasks */
        if ( $user_id && ! user_can( $user_id, 'ct_manage_tasks' ) ) {
            $args['meta_query'][] = array(
                'key'     => '_ct_assigned',
                'value'   => sprintf( ':%d;', $user_id ),
                'compare' => 'LIKE',
            );
        }

        if ( ! empty( $filters['assigned'] ) ) {
            $assigned_id = absint( $filters['assigned'] );
            $args['meta_query'][] = array(
                'key'     => '_ct_assigned',
                'value'   => sprintf( ':%d;', $assigned_id ),
                'compare' => 'LIKE',
            );
        }

        $posts = get_posts( $args );
        return array_map( array( __CLASS__, 'format' ), $posts );
    }

    public static function reorder( array $items ): void {
        foreach ( $items as $item ) {
            $id = absint( $item['id'] ?? 0 );
            if ( ! $id ) {
                continue;
            }
            if ( isset( $item['position'] ) ) {
                update_post_meta( $id, '_ct_position', absint( $item['position'] ) );
            }
            if ( isset( $item['status'] ) ) {
                update_post_meta( $id, '_ct_status', sanitize_text_field( $item['status'] ) );
            }
        }
    }

    private static function get_next_position( int $list_id ): int {
        global $wpdb;
        $max = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT MAX( CAST( pm2.meta_value AS UNSIGNED ) )
             FROM {$wpdb->postmeta} pm1
             INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
             WHERE pm1.meta_key = '_ct_list_id' AND pm1.meta_value = %d
               AND pm2.meta_key = '_ct_position'",
            $list_id
        ) );
        return $max + 1;
    }

    public static function format( WP_Post $post ): array {
        $assigned = get_post_meta( $post->ID, '_ct_assigned', true );
        if ( ! is_array( $assigned ) ) {
            $assigned = array();
        }

        $assigned_users = array();
        foreach ( $assigned as $uid ) {
            $user = get_userdata( $uid );
            if ( $user ) {
                $assigned_users[] = array(
                    'id'     => $uid,
                    'name'   => $user->display_name,
                    'avatar' => get_avatar_url( $uid, array( 'size' => 32 ) ),
                );
            }
        }

        $tags  = wp_get_object_terms( $post->ID, 'ct_tag', array( 'fields' => 'names' ) );
        if ( is_wp_error( $tags ) ) {
            $tags = array();
        }

        return array(
            'id'          => $post->ID,
            'title'       => $post->post_title,
            'description' => $post->post_content,
            'list_id'     => (int) get_post_meta( $post->ID, '_ct_list_id', true ),
            'priority'    => get_post_meta( $post->ID, '_ct_priority', true ) ?: 'normal',
            'status'      => get_post_meta( $post->ID, '_ct_status', true ) ?: 'To Do',
            'assigned'    => $assigned_users,
            'due_date'    => get_post_meta( $post->ID, '_ct_due_date', true ) ?: '',
            'position'    => (int) get_post_meta( $post->ID, '_ct_position', true ),
            'tags'        => $tags,
            'author'      => (int) $post->post_author,
            'created'     => $post->post_date,
        );
    }
}
