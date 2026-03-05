<?php
defined( 'ABSPATH' ) || exit;

class CT_Shortcode {

    public static function init(): void {
        add_shortcode( 'clicktasks', array( __CLASS__, 'render' ) );
    }

    public static function render( $atts ): string {
        if ( ! is_user_logged_in() || ! current_user_can( 'ct_access_app' ) ) {
            return '<div style="padding:2rem;text-align:center;color:#9CA3AF">' .
                   esc_html__( 'No tienes permisos para acceder a ClickTasks.', 'clicktasks' ) .
                   '</div>';
        }

        ob_start();
        include CT_PLUGIN_DIR . 'templates/app-shell.php';
        return ob_get_clean();
    }
}
