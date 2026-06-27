<?php
/**
 * Custom User Management for GymnasticsHub
 * Roles: User, Club, Organizer + Approval System
 */

// 1. Create custom roles
function gh_add_custom_roles() {
    $roles = array(
        'gh_user'      => 'Пользователь',
        'gh_club'      => 'Клуб',
        'gh_organizer' => 'Организатор',
        'gh_coach'     => 'Тренер',
        'gh_manager'   => 'Менеджер Gymnastics Hub'
    );

    foreach ($roles as $role_key => $role_name) {
        $caps = array(
            'read'         => true,
            'upload_files' => true,
        );

        // Manager specific caps
        if ($role_key === 'gh_manager') {
            $caps = array_merge($caps, array(
                'edit_posts'             => true,
                'edit_others_posts'      => true,
                'publish_posts'          => true,
                'edit_published_posts'   => true,
                'read_private_posts'     => true,
                'edit_private_posts'     => true,
                'delete_posts'           => true,
                'delete_published_posts' => true,
                'delete_others_posts'    => true,
                'delete_private_posts'   => true,
                'manage_categories'      => true,
                'moderate_comments'      => true,
                'manage_gh_settings'     => true,
            ));
        }

        add_role($role_key, $role_name, $caps);

        // Force update capabilities if role already exists
        $role_object = get_role($role_key);
        if ($role_object) {
            foreach ($caps as $cap => $grant) {
                if ($grant) {
                    $role_object->add_cap($cap);
                } else {
                    $role_object->remove_cap($cap);
                }
            }
        }
    }
}
add_action('init', 'gh_add_custom_roles');

// 2. Default status is handled in ajax-auth.php during registration

// 3. Admin Menu with "Pending" tab
add_action('admin_menu', 'gh_add_hub_members_menu');
function gh_add_hub_members_menu() {
    // Count pending users for the badge
    $pending_count = count(get_users(array('meta_key' => 'gh_status', 'meta_value' => 'pending')));
    $badge = $pending_count ? " <span class='update-plugins count-$pending_count'><span class='plugin-count'>$pending_count</span></span>" : "";

    add_menu_page(
        'Участники' . $badge,
        'Участники' . $badge,
        'manage_options',
        'hub_members',
        'gh_render_hub_members_page',
        'dashicons-groups',
        10
    );
    
    add_submenu_page('hub_members', 'На одобрении' . $badge, 'На одобрении' . $badge, 'manage_options', 'hub_pending', 'gh_render_pending_page');
    add_submenu_page('hub_members', 'Клубы', 'Клубы', 'manage_options', 'users.php?role=gh_club');
    add_submenu_page('hub_members', 'Тренеры', 'Тренеры', 'manage_options', 'users.php?role=gh_coach');
    add_submenu_page('hub_members', 'Организаторы', 'Организаторы', 'manage_options', 'users.php?role=gh_organizer');
}

// 4. Render Pages
function gh_render_hub_members_page() {
    gh_render_custom_user_list('Все одобренные участники', 'approved');
}

function gh_render_pending_page() {
    gh_render_custom_user_list('Ожидают одобрения', 'pending');
}

function gh_render_custom_user_list($title, $status) {
    if (!current_user_can('manage_options')) return;

    // Handle Approve Action
    if (isset($_GET['action']) && $_GET['action'] === 'approve' && isset($_GET['user'])) {
        update_user_meta(intval($_GET['user']), 'gh_status', 'approved');
        echo '<div class="updated"><p>Пользователь одобрен!</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">' . $title . '</h1>';
    echo '<hr class="wp-header-end">';
    
    // Force include only Hub roles
    $_GET['role'] = 'gh_user,gh_club,gh_organizer,gh_coach,gh_manager'; 
    
    // Filter by our status meta
    add_action('pre_get_users', function($query) use ($status) {
        if (is_admin() && $query->get('role') === 'gh_user,gh_club,gh_organizer,gh_coach,gh_manager') {
            $query->set('meta_key', 'gh_status');
            $query->set('meta_value', $status);
        }
    });
    
    require_once(ABSPATH . 'wp-admin/includes/class-wp-users-list-table.php');
    $user_table = new WP_Users_List_Table();
    $user_table->prepare_items();
    $user_table->display();
    
    echo '</div>';
}

// 5. Add "Approve" button to row actions for pending users
add_filter('user_row_actions', 'gh_add_approve_action', 10, 2);
function gh_add_approve_action($actions, $user) {
    $status = get_user_meta($user->ID, 'gh_status', true);
    if ($status === 'pending') {
        $url = admin_url('admin.php?page=hub_pending&action=approve&user=' . $user->ID);
        $actions['approve'] = '<a href="' . $url . '" style="color: #008000; font-weight: bold;">Одобрить</a>';
    }
    return $actions;
}

// 6. Custom Columns (Status Badge)
add_filter('manage_users_columns', 'gh_modify_hub_columns_v2');
function gh_modify_hub_columns_v2($columns) {
    $columns['gh_status'] = 'Статус';
    return $columns;
}

add_filter('manage_users_custom_column', 'gh_hub_column_content_v2', 10, 3);
function gh_hub_column_content_v2($value, $column_name, $user_id) {
    if ($column_name === 'gh_status') {
        $status = get_user_meta($user_id, 'gh_status', true);
        if ($status === 'unverified') return '<span class="status-unverified" style="background: #e2e3e5; padding: 4px 8px; border-radius: 4px; color: #383d41;">✉️ Не подтвержден</span>';
        if ($status === 'pending') return '<span class="status-pending" style="background: #ffecb3; padding: 4px 8px; border-radius: 4px; color: #856404;">⏳ Ожидает</span>';
        return '<span class="status-approved" style="background: #c3e6cb; padding: 4px 8px; border-radius: 4px; color: #155724;">✅ Одобрен</span>';
    }
    return $value;
}

/**
 * 6.5. Exclude Hub Users from default Users list
 */
add_action('pre_get_users', 'gh_exclude_custom_users_from_wp_list');
function gh_exclude_custom_users_from_wp_list($query) {
    if (!is_admin()) return;

    $screen = get_current_screen();
    // If we are on the default 'users' screen (users.php)
    if ($screen && $screen->id === 'users' && !isset($_GET['page'])) {
        // If no specific role is requested, or we are on the 'All' tab
        if (!isset($_GET['role']) || $_GET['role'] === 'all') {
            $query->set('role__not_in', array('gh_user', 'gh_club', 'gh_organizer', 'gh_coach', 'gh_manager'));
        }
    }
}

/**
 * 7. Admin Menu Cleanup & Reorder
 */
add_action('admin_menu', 'gh_cleanup_and_reorder_menu', 999);
function gh_cleanup_and_reorder_menu() {
    if (current_user_can('gh_manager') && !current_user_can('administrator')) {
        // Hide standard WP items for managers
        remove_menu_page('index.php');                  // Dashboard
        remove_menu_page('edit.php');                   // Posts
        remove_menu_page('edit.php?post_type=page');    // Pages
        remove_menu_page('edit-comments.php');          // Comments
        remove_menu_page('themes.php');                 // Appearance
        remove_menu_page('plugins.php');                // Plugins
        remove_menu_page('users.php');                  // Users
        remove_menu_page('tools.php');                  // Tools
        remove_menu_page('options-general.php');        // Settings
        remove_menu_page('profile.php');                // Profile
    }
}

/**
 * Redirect Manager to Events by default
 */
add_action('admin_init', 'gh_manager_admin_redirect');
function gh_manager_admin_redirect() {
    global $pagenow;
    if (current_user_can('gh_manager') && !current_user_can('administrator')) {
        // If landing on Dashboard or standard Posts, go to Events
        if ($pagenow === 'index.php' || ($pagenow === 'edit.php' && !isset($_GET['post_type']))) {
            wp_redirect(admin_url('edit.php?post_type=gh_event'));
            exit;
        }
    }
}

/**
 * Custom Menu Order
 */
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'gh_custom_menu_order');

function gh_custom_menu_order($menu_ord) {
    if (!$menu_ord) return true;

    // Define the desired order
    $new_order = array(
        'edit.php?post_type=gh_event',      // Мероприятия
        'edit.php?post_type=ad_request',    // Заявки на рекламу
        'edit.php?post_type=gh_ad',         // Объявления
        'edit.php?post_type=forum_topic',    // Форум
        'edit.php?post_type=gallery_album', // Альбомы
        'gh-theme-settings',                // Настройки Gymnastics Hub
    );

    // Merge with existing menu to keep other items at the bottom
    return array_merge($new_order, array_diff($menu_ord, $new_order));
}
