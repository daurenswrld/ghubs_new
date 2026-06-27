<?php
/**
 * Security and Access Control for GymnasticsHub
 */

// 1. Hide Admin Bar for non-administrators
add_filter('show_admin_bar', 'gh_hide_admin_bar_for_users');
function gh_hide_admin_bar_for_users($show) {
    if (!current_user_can('manage_options') && !current_user_can('gh_manager')) {
        return false;
    }
    return $show;
}

// 2. Block WP-Admin access for non-administrators
add_action('admin_init', 'gh_block_admin_access');
function gh_block_admin_access() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    if (!current_user_can('manage_options') && !current_user_can('gh_manager')) {
        wp_redirect(home_url('/profile/'));
        exit;
    }
}

// 3. Remove WordPress version for security
remove_action('wp_head', 'wp_generator');

// 4. Disable XML-RPC (often used for brute-force attacks)
add_filter('xmlrpc_enabled', '__return_false');

// 5. Hide login errors (don't tell if user exists or password is wrong on standard forms)
add_filter('login_errors', function() {
    return 'Ошибка авторизации. Пожалуйста, проверьте данные.';
});

// 6. Redirect wp-login.php to custom login page (except for logout and reset)
add_action('init', 'gh_redirect_login_page');
function gh_redirect_login_page() {
    global $pagenow;
    
    // Allow bypass if ?admin=1 is present
    if (isset($_GET['admin'])) {
        return;
    }

    if ($pagenow == 'wp-login.php' && !isset($_GET['action']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
        $query_string = $_SERVER['QUERY_STRING'];
        $redirect_to = home_url('/login/');
        
        if ($query_string) {
            $redirect_to = add_query_arg(array(), $redirect_to . '?' . $query_string);
        }

        wp_redirect($redirect_to);
        exit;
    }
}
