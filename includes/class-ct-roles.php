<?php
defined( 'ABSPATH' ) || exit;

class CT_Roles {

    private static array $cpt_caps = array(
        'edit_ct_items',
        'edit_others_ct_items',
        'publish_ct_items',
        'read_private_ct_items',
        'delete_ct_items',
        'delete_others_ct_items',
        'delete_published_ct_items',
        'edit_published_ct_items',
    );

    public static function activate(): void {
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->add_cap( 'ct_manage_workspaces' );
            $admin->add_cap( 'ct_manage_tasks' );
            $admin->add_cap( 'ct_access_app' );
            foreach ( self::$cpt_caps as $cap ) {
                $admin->add_cap( $cap );
            }
        }

        $editor = get_role( 'editor' );
        if ( $editor ) {
            $editor->add_cap( 'ct_manage_tasks' );
            $editor->add_cap( 'ct_access_app' );
            foreach ( self::$cpt_caps as $cap ) {
                $editor->add_cap( $cap );
            }
        }

        $author = get_role( 'author' );
        if ( $author ) {
            $author->add_cap( 'ct_access_app' );
            $author->add_cap( 'edit_ct_items' );
            $author->add_cap( 'publish_ct_items' );
            $author->add_cap( 'delete_ct_items' );
            $author->add_cap( 'edit_published_ct_items' );
        }
    }

    public static function deactivate(): void {
        $all_caps = array_merge(
            array( 'ct_manage_workspaces', 'ct_manage_tasks', 'ct_access_app' ),
            self::$cpt_caps
        );

        foreach ( wp_roles()->roles as $role_name => $role_data ) {
            $role = get_role( $role_name );
            if ( $role ) {
                foreach ( $all_caps as $cap ) {
                    $role->remove_cap( $cap );
                }
            }
        }
    }
}
