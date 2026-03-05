<?php defined( 'ABSPATH' ) || exit; ?>

<!-- Logo area -->
<div class="ct-flex ct-items-center ct-gap-2 ct-px-4" style="height:60px">
    <div class="ct-w-7 ct-h-7 ct-rounded-lg ct-flex ct-items-center ct-justify-center ct-font-bold ct-text-white ct-text-[11px]" style="background:linear-gradient(135deg,#8B5CF6,#6366F1)">CU</div>
    <div class="ct-flex ct-items-center ct-gap-1 ct-flex-1 ct-min-w-0">
        <span class="ct-text-[13px] ct-font-semibold ct-text-white ct-truncate">ClickTasks</span>
        <i class="fa-solid fa-chevron-down ct-text-[10px] ct-text-[#71717A] ct-shrink-0"></i>
    </div>
    <button id="ct-sidebar-close" class="ct-hidden max-md:ct-block ct-p-1 ct-text-[#71717A] hover:ct-text-white">
        <i class="fa-solid fa-xmark ct-text-sm"></i>
    </button>
</div>

<!-- Search -->
<div class="ct-px-3 ct-pb-2">
    <div class="ct-flex ct-items-center ct-gap-1.5 ct-bg-ct-sidebar-b ct-rounded-lg ct-px-2.5" style="height:32px">
        <i class="fa-solid fa-magnifying-glass ct-text-xs ct-text-[#71717A]"></i>
        <span class="ct-text-xs ct-text-[#71717A]">Search...</span>
    </div>
</div>

<!-- Nav items -->
<div class="ct-px-2 ct-py-1" style="display:flex;flex-direction:column;gap:2px">
    <div class="ct-flex ct-items-center ct-gap-2 ct-px-2 ct-rounded-md ct-text-ct-sidebar-t hover:ct-bg-white/5 ct-cursor-pointer" style="height:36px">
        <i class="fa-solid fa-house ct-text-sm"></i>
        <span class="ct-text-[13px]">Home</span>
    </div>
    <div class="ct-flex ct-items-center ct-gap-2 ct-px-2 ct-rounded-md ct-cursor-pointer" style="height:36px;background:rgba(124,58,237,0.12)">
        <i class="fa-solid fa-check" style="font-size:14px;color:#A78BFA"></i>
        <span class="ct-text-[13px]" style="color:#A78BFA;font-weight:600">My Work</span>
    </div>
</div>

<!-- Divider -->
<div class="ct-h-px ct-bg-ct-sidebar-b"></div>

<!-- SPACES header -->
<div class="ct-flex ct-items-center ct-justify-between ct-px-4 ct-pt-3 ct-pb-1">
    <span style="font-size:10px;font-weight:600;color:#5A5A7A;letter-spacing:1px">SPACES</span>
    <?php if ( current_user_can( 'ct_manage_workspaces' ) ) : ?>
    <button id="ct-btn-add-workspace" class="ct-text-[#71717A] hover:ct-text-white ct-transition-colors">
        <i class="fa-solid fa-plus ct-text-xs"></i>
    </button>
    <?php endif; ?>
</div>

<!-- Tree -->
<div class="ct-flex-1 ct-overflow-y-auto ct-px-2 ct-py-1" id="ct-sidebar-tree">
    <!-- JS rendered -->
</div>

<!-- User area -->
<div class="ct-flex ct-items-center ct-gap-2 ct-px-4 ct-bg-ct-sidebar-d ct-shrink-0" style="height:52px">
    <div class="ct-w-7 ct-h-7 ct-rounded-full ct-overflow-hidden ct-shrink-0">
        <img src="<?php echo esc_url( get_avatar_url( get_current_user_id(), array( 'size' => 32 ) ) ); ?>" class="ct-w-full ct-h-full ct-object-cover" alt="">
    </div>
    <span class="ct-text-[13px] ct-font-medium ct-text-[#CCCCDD] ct-truncate ct-flex-1"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
    <i class="fa-solid fa-gear ct-text-sm ct-text-[#71717A]"></i>
</div>
