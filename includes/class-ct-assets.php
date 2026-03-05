<?php
defined( 'ABSPATH' ) || exit;

class CT_Assets {

    public static function init(): void {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
    }

    public static function enqueue(): void {
        global $post;

        if ( ! $post || ! has_shortcode( $post->post_content, 'clicktasks' ) ) {
            return;
        }

        /* Font Awesome */
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            array(),
            '6.5.1'
        );

        /* Tailwind CDN */
        wp_enqueue_script(
            'tailwindcss',
            'https://cdn.tailwindcss.com',
            array(),
            null,
            false
        );

        /* Tailwind config inline */
        wp_add_inline_script( 'tailwindcss', self::tailwind_config(), 'after' );

        /* SortableJS */
        wp_enqueue_script(
            'sortablejs',
            CT_PLUGIN_URL . 'assets/vendor/Sortable.min.js',
            array(),
            '1.15.0',
            true
        );

        /* Custom CSS */
        wp_enqueue_style(
            'clicktasks',
            CT_PLUGIN_URL . 'assets/css/clicktasks.css',
            array(),
            CT_VERSION
        );

        /* Full-width override for FSE themes (TT5 etc.) */
        wp_add_inline_style( 'clicktasks',
            '.entry-content { max-width: none !important; }
             .wp-site-blocks .entry-content > * { max-width: none !important; }
             .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)) { max-width: none !important; }
             .wp-site-blocks > main .entry-content { padding-left: 0 !important; padding-right: 0 !important; }
             .wp-block-post-content { padding: 0 !important; }'
        );

        /* JS Modules */
        $modules = array(
            'ct-utils'       => 'utils.js',
            'ct-ajax-helper' => 'ajax-helper.js',
            'ct-modal'       => 'modal.js',
            'ct-router'      => 'router.js',
            'ct-forms'       => 'forms.js',
            'ct-sidebar'     => 'sidebar.js',
            'ct-filters'     => 'filters.js',
            'ct-kanban'      => 'kanban.js',
            'ct-list-view'   => 'list-view.js',
            'ct-task-detail' => 'task-detail.js',
            'ct-comments'    => 'comments.js',
        );

        $prev = array( 'sortablejs' );
        foreach ( $modules as $handle => $file ) {
            wp_enqueue_script(
                $handle,
                CT_PLUGIN_URL . 'assets/js/modules/' . $file,
                $prev,
                CT_VERSION,
                true
            );
            $prev = array( $handle );
        }

        /* Main entry */
        wp_enqueue_script(
            'clicktasks-app',
            CT_PLUGIN_URL . 'assets/js/clicktasks.js',
            array( 'ct-comments' ),
            CT_VERSION,
            true
        );

        /* Localize data */
        $user = wp_get_current_user();
        wp_localize_script( 'ct-utils', 'ctData', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'ct_nonce' ),
            'currentUser' => array(
                'id'     => $user->ID,
                'name'   => $user->display_name,
                'avatar' => get_avatar_url( $user->ID, array( 'size' => 32 ) ),
            ),
            'caps' => array(
                'manageWorkspaces' => current_user_can( 'ct_manage_workspaces' ),
                'manageTasks'      => current_user_can( 'ct_manage_tasks' ),
            ),
            'i18n' => array(
                'confirm_delete'   => __( '¿Estás seguro de que quieres eliminar esto?', 'clicktasks' ),
                'loading'          => __( 'Cargando...', 'clicktasks' ),
                'no_tasks'         => __( 'No hay tareas. ¡Crea la primera!', 'clicktasks' ),
                'select_list'      => __( 'Selecciona una lista del sidebar', 'clicktasks' ),
                'new_task'         => __( '+ Nueva Tarea', 'clicktasks' ),
                'save'             => __( 'Guardar', 'clicktasks' ),
                'cancel'           => __( 'Cancelar', 'clicktasks' ),
                'delete'           => __( 'Eliminar', 'clicktasks' ),
                'title'            => __( 'Título', 'clicktasks' ),
                'description'      => __( 'Descripción', 'clicktasks' ),
                'priority'         => __( 'Prioridad', 'clicktasks' ),
                'status'           => __( 'Estado', 'clicktasks' ),
                'assigned'         => __( 'Asignados', 'clicktasks' ),
                'due_date'         => __( 'Fecha límite', 'clicktasks' ),
                'tags'             => __( 'Etiquetas', 'clicktasks' ),
                'comments'         => __( 'Comentarios', 'clicktasks' ),
                'add_comment'      => __( 'Añadir comentario...', 'clicktasks' ),
                'workspace'        => __( 'Workspace', 'clicktasks' ),
                'folder'           => __( 'Carpeta', 'clicktasks' ),
                'list'             => __( 'Lista', 'clicktasks' ),
                'all_statuses'     => __( 'Todos los estados', 'clicktasks' ),
                'all_priorities'   => __( 'Todas las prioridades', 'clicktasks' ),
                'all_users'        => __( 'Todos los usuarios', 'clicktasks' ),
                'urgent'           => __( 'Urgente', 'clicktasks' ),
                'high'             => __( 'Alta', 'clicktasks' ),
                'normal'           => __( 'Normal', 'clicktasks' ),
                'low'              => __( 'Baja', 'clicktasks' ),
                'kanban'           => __( 'Kanban', 'clicktasks' ),
                'list_view'        => __( 'Listado', 'clicktasks' ),
            ),
        ) );
    }

    private static function tailwind_config(): string {
        return "tailwind.config = {
            prefix: 'ct-',
            important: '#ct-app',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        'ct-bg':       '#F7F8FA',
                        'ct-sidebar':  '#1C1C2E',
                        'ct-sidebar-d':'#16162A',
                        'ct-sidebar-b':'#2A2A3E',
                        'ct-sidebar-t':'#A0A0B8',
                        'ct-card':     '#FFFFFF',
                        'ct-border':   '#E5E7EB',
                        'ct-border-l': '#F4F4F5',
                        'ct-primary':  '#7C3AED',
                        'ct-primary-h':'#6D28D9',
                        'ct-indigo':   '#6366F1',
                        'ct-text':     '#111827',
                        'ct-text-s':   '#6B7280',
                        'ct-muted':    '#9CA3AF',
                        'ct-surface':  '#F9FAFB',
                        'ct-surface-2':'#F3F4F6',
                        'ct-urgent':   '#EF4444',
                        'ct-high':     '#F97316',
                        'ct-normal':   '#3B82F6',
                        'ct-low':      '#9CA3AF',
                        'ct-todo':     '#9CA3AF',
                        'ct-progress': '#3B82F6',
                        'ct-review':   '#F59E0B',
                        'ct-done':     '#10B981',
                    }
                }
            }
        };";
    }
}
