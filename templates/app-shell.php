<?php defined( 'ABSPATH' ) || exit; ?>

<div id="ct-app" class="ct-font-sans ct-text-ct-text ct-bg-ct-bg ct-rounded-lg ct-overflow-hidden ct-relative" style="height:80vh;max-width:100%">
    <div class="ct-flex ct-h-full">

        <!-- Sidebar -->
        <aside id="ct-sidebar" class="ct-w-[240px] ct-bg-ct-sidebar ct-flex ct-flex-col ct-shrink-0 ct-h-full ct-overflow-hidden max-md:ct-fixed max-md:ct-inset-y-0 max-md:ct-left-0 max-md:ct-z-50 max-md:ct-transition-transform" data-open="true">
            <?php include CT_PLUGIN_DIR . 'templates/sidebar.php'; ?>
        </aside>

        <!-- Sidebar overlay mobile -->
        <div id="ct-sidebar-overlay" class="ct-hidden max-md:ct-fixed max-md:ct-inset-0 max-md:ct-bg-black/50 max-md:ct-z-40" style="display:none"></div>

        <!-- Main Area -->
        <main class="ct-flex-1 ct-flex ct-flex-col ct-overflow-hidden ct-min-w-0 ct-bg-ct-bg">

            <!-- Subheader / Topbar -->
            <header class="ct-flex ct-items-center ct-justify-between ct-px-5 ct-bg-white ct-border-b ct-border-ct-border ct-shrink-0" style="height:48px">
                <div class="ct-flex ct-items-center ct-gap-3">
                    <!-- Mobile hamburger -->
                    <button id="ct-sidebar-toggle" class="ct-hidden max-md:ct-block ct-p-1 ct-text-ct-muted hover:ct-text-ct-text">
                        <i class="fa-solid fa-bars ct-text-base"></i>
                    </button>
                    <div id="ct-breadcrumb" class="ct-flex ct-items-center ct-gap-1.5 ct-text-[13px]"></div>
                </div>
                <div class="ct-flex ct-items-center ct-gap-3">
                    <!-- View Switcher -->
                    <div id="ct-view-toggle" class="ct-flex ct-items-center ct-rounded-lg ct-bg-ct-surface-2 ct-p-1 ct-gap-1">
                        <button data-view="kanban" class="ct-flex ct-items-center ct-gap-1.5 ct-px-2.5 ct-py-1 ct-text-[13px] ct-font-semibold ct-rounded-md ct-bg-white ct-text-ct-indigo ct-shadow-sm">
                            <i class="fa-solid fa-table-columns ct-text-xs"></i>
                            Kanban
                        </button>
                        <button data-view="list" class="ct-flex ct-items-center ct-gap-1.5 ct-px-2.5 ct-py-1 ct-text-[13px] ct-rounded-md ct-text-ct-text-s hover:ct-text-ct-text">
                            <i class="fa-solid fa-list ct-text-xs"></i>
                            List
                        </button>
                    </div>

                    <!-- Filter btn -->
                    <button id="ct-filter-toggle" class="ct-flex ct-items-center ct-gap-1.5 ct-px-3 ct-py-1.5 ct-text-[13px] ct-text-ct-text-s ct-rounded-md ct-border ct-border-ct-border hover:ct-border-ct-muted" style="display:none">
                        <i class="fa-solid fa-sliders ct-text-xs"></i>
                        Filter
                    </button>

                    <!-- New Task btn -->
                    <button id="ct-btn-new-task" class="ct-flex ct-items-center ct-gap-1.5 ct-bg-ct-primary hover:ct-bg-ct-primary-h ct-text-white ct-px-3.5 ct-py-1.5 ct-rounded-md ct-text-[13px] ct-font-semibold ct-transition-colors" style="display:none">
                        <i class="fa-solid fa-plus ct-text-xs"></i>
                        New Task
                    </button>
                </div>
            </header>

            <!-- Filters Bar (hidden by default) -->
            <?php include CT_PLUGIN_DIR . 'templates/partials/filters-bar.php'; ?>

            <!-- Content Area -->
            <div id="ct-content" class="ct-flex-1 ct-overflow-auto">
                <div id="ct-empty-state" class="ct-flex ct-items-center ct-justify-center ct-h-full">
                    <div class="ct-text-center">
                        <i class="fa-regular fa-clipboard ct-text-5xl ct-text-ct-border ct-mb-4"></i>
                        <p class="ct-text-ct-muted ct-text-sm"><?php esc_html_e( 'Selecciona una lista del sidebar', 'clicktasks' ); ?></p>
                    </div>
                </div>
                <div id="ct-loading" class="ct-hidden ct-flex ct-items-center ct-justify-center ct-h-full">
                    <div class="ct-animate-spin ct-w-8 ct-h-8 ct-border-2 ct-border-ct-primary ct-border-t-transparent ct-rounded-full"></div>
                </div>
                <div id="ct-kanban-view" class="ct-hidden ct-h-full"></div>
                <div id="ct-list-view" class="ct-hidden"></div>
            </div>
        </main>

        <!-- Task Detail Panel -->
        <div id="ct-task-panel" class="ct-fixed ct-top-0 ct-right-0 ct-h-full ct-w-[860px] max-md:ct-w-full ct-bg-white ct-z-50 ct-translate-x-full ct-transition-transform ct-duration-200 ct-flex ct-flex-col ct-overflow-hidden" style="border-radius:12px 0 0 12px;box-shadow:0 8px 32px rgba(0,0,0,0.19)">
            <div id="ct-task-panel-content" class="ct-flex-1 ct-overflow-y-auto"></div>
        </div>
        <div id="ct-task-panel-overlay" class="ct-fixed ct-inset-0 ct-z-40" style="display:none;background:rgba(26,26,46,0.5)"></div>
    </div>

    <!-- Modal Overlay -->
    <div id="ct-modal" class="ct-fixed ct-inset-0 ct-z-[60] ct-flex ct-items-center ct-justify-center ct-bg-[#1A1A2E]/50" style="display:none">
        <div id="ct-modal-body" class="ct-bg-white ct-rounded-xl ct-shadow-2xl ct-w-full ct-max-w-lg ct-mx-4 ct-border ct-border-ct-border"></div>
    </div>
</div>
