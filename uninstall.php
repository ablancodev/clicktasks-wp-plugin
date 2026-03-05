<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/* Remove all CPT posts */
$types = array( 'ct_task', 'ct_list', 'ct_folder', 'ct_workspace' );
foreach ( $types as $type ) {
    $posts = get_posts( array(
        'post_type'      => $type,
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ) );
    foreach ( $posts as $id ) {
        wp_delete_post( $id, true );
    }
}

/* Remove taxonomy terms */
$terms = get_terms( array(
    'taxonomy'   => 'ct_tag',
    'hide_empty' => false,
    'fields'     => 'ids',
) );
if ( ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term_id ) {
        wp_delete_term( $term_id, 'ct_tag' );
    }
}

/* Remove capabilities */
$caps = array(
    'ct_manage_workspaces',
    'ct_manage_tasks',
    'ct_access_app',
);

$cpt_caps = array(
    'edit_ct_items',
    'edit_others_ct_items',
    'publish_ct_items',
    'read_private_ct_items',
    'delete_ct_items',
    'delete_others_ct_items',
    'delete_published_ct_items',
    'edit_published_ct_items',
);

$all_caps = array_merge( $caps, $cpt_caps );

foreach ( wp_roles()->roles as $role_name => $role_data ) {
    $role = get_role( $role_name );
    if ( $role ) {
        foreach ( $all_caps as $cap ) {
            $role->remove_cap( $cap );
        }
    }
}
