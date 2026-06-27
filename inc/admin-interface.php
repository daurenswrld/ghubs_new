<?php
/**
 * Admin Interface Customization for GymnasticsHub
 */

// 1. Clean up Admin Menu (Remove unnecessary items)
add_action('admin_menu', 'gh_cleanup_admin_menu', 999);
function gh_cleanup_admin_menu() {
    remove_menu_page('edit.php'); // Posts
    remove_menu_page('edit-comments.php'); // Comments
}

// 2. Custom Welcome Dashboard Widget
add_action('wp_dashboard_setup', 'gh_custom_dashboard_widgets');
function gh_custom_dashboard_widgets() {
    wp_add_dashboard_widget('gh_welcome_widget', '👋 Добро пожаловать в GymnasticsHub', 'gh_render_welcome_widget');
}

function gh_render_welcome_widget() {
    ?>
    <div class="gh-dashboard-welcome">
        <h2 style="margin-top: 0;">Рады вас видеть!</h2>
        <p>Здесь вы можете управлять всеми аспектами вашей платформы.</p>
        <div class="gh-quick-actions" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
            <a href="post-new.php?post_type=events" class="button button-primary button-hero" style="text-align: center; display: block;">🏆 Добавить мероприятие</a>
            <a href="admin.php?page=hub_members" class="button button-secondary button-hero" style="text-align: center; display: block;">👥 Новые участники</a>
            <a href="post-new.php?post_type=ads" class="button button-secondary button-hero" style="text-align: center; display: block;">📢 Разместить рекламу</a>
            <a href="edit.php?post_type=page" class="button button-secondary button-hero" style="text-align: center; display: block;">📄 Управление страницами</a>
        </div>
    </div>
    <style>
        #gh_welcome_widget { background: #f0f0f1; border: 2px solid #2271b1; }
        .gh-quick-actions .button { padding: 10px !important; height: auto !important; line-height: 1.4 !important; }
    </style>
    <?php
}

// 3. Custom Admin CSS for Premium Look
add_action('admin_head', 'gh_admin_custom_styles');
function gh_admin_custom_styles() {
    echo '<style>
        /* Custom Admin Bar Color */
        #wpadminbar { background: #000 !important; }
        #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap { background-color: #111 !important; }
        #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu .wp-menu-arrow, #adminmenu .wp-menu-arrow div, #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, .gui-folder-tab-active, .gui-folder-tab-active:hover { background: #2271b1 !important; }
        
        /* Modern table look */
        .wp-list-table { border-radius: 8px; overflow: hidden; border: none !important; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .wp-list-table thead th { background: #f9f9f9; padding: 15px 10px !important; }
        
        /* Dashboard cleanup */
        #dashboard-widgets-container .postbox-container { width: 100% !important; }
        #welcome-panel { display: none !important; }
    </style>';
}

// 4. Custom Admin Footer
add_filter('admin_footer_text', 'gh_custom_admin_footer');
function gh_custom_admin_footer() {
    echo 'Панель управления <a href="' . home_url() . '" target="_blank">GymnasticsHub</a>. Разработано для чемпионов.';
}
