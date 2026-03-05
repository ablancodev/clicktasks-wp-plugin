<?php defined( 'ABSPATH' ) || exit; ?>

<div id="ct-filters-bar" class="ct-px-5 ct-py-2 ct-border-b ct-border-ct-border ct-bg-white ct-shrink-0" style="display:none">
    <div class="ct-flex ct-items-center ct-gap-3 ct-flex-wrap">
        <select id="ct-filter-status" class="ct-bg-white ct-text-ct-text ct-border ct-border-ct-border ct-rounded-md ct-px-2.5 ct-py-1.5 ct-text-[13px] focus:ct-border-ct-primary focus:ct-ring-1 focus:ct-ring-ct-primary/20">
            <option value=""><?php esc_html_e( 'Todos los estados', 'clicktasks' ); ?></option>
        </select>
        <select id="ct-filter-priority" class="ct-bg-white ct-text-ct-text ct-border ct-border-ct-border ct-rounded-md ct-px-2.5 ct-py-1.5 ct-text-[13px] focus:ct-border-ct-primary focus:ct-ring-1 focus:ct-ring-ct-primary/20">
            <option value=""><?php esc_html_e( 'Todas las prioridades', 'clicktasks' ); ?></option>
            <option value="urgent"><?php esc_html_e( 'Urgente', 'clicktasks' ); ?></option>
            <option value="high"><?php esc_html_e( 'Alta', 'clicktasks' ); ?></option>
            <option value="normal"><?php esc_html_e( 'Normal', 'clicktasks' ); ?></option>
            <option value="low"><?php esc_html_e( 'Baja', 'clicktasks' ); ?></option>
        </select>
        <select id="ct-filter-assigned" class="ct-bg-white ct-text-ct-text ct-border ct-border-ct-border ct-rounded-md ct-px-2.5 ct-py-1.5 ct-text-[13px] focus:ct-border-ct-primary focus:ct-ring-1 focus:ct-ring-ct-primary/20">
            <option value=""><?php esc_html_e( 'Todos los usuarios', 'clicktasks' ); ?></option>
        </select>
    </div>
</div>
