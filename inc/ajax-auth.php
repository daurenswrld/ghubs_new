<?php
/**
 * AJAX Authentication Handler for GymnasticsHub
 */

// 1. AJAX Login Handler
add_action('wp_ajax_gh_ajax_login', 'gh_ajax_login');
add_action('wp_ajax_nopriv_gh_ajax_login', 'gh_ajax_login');
function gh_ajax_login() {
    check_ajax_referer('gh-auth-nonce', 'nonce');
    $info = array();
    $info['user_login'] = sanitize_text_field($_POST['username']);
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;
    $user_signon = wp_signon($info, false);
    if (is_wp_error($user_signon)) {
        wp_send_json_error(array('message' => 'Неверный логин или пароль.'));
    } else {
        $redirect_url = home_url('/profile/');
        if (!empty($_POST['redirect_to'])) {
            $redirect_url = esc_url_raw($_POST['redirect_to']);
        }
        wp_send_json_success(array('message' => 'Успешный вход! Перенаправляем...', 'redirect' => $redirect_url));
    }
    die();
}

// 2. AJAX Registration Handler
add_action('wp_ajax_gh_ajax_register', 'gh_ajax_register');
add_action('wp_ajax_nopriv_gh_ajax_register', 'gh_ajax_register');
function gh_ajax_register() {
    check_ajax_referer('gh-auth-nonce', 'nonce');
    $name = sanitize_text_field(wp_unslash($_POST['full_name']));
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = sanitize_text_field($_POST['role']); 
    if (!preg_match('/^(?=.*[A-Z]).{6,}$/', $password)) { wp_send_json_error(array('message' => 'Пароль должен быть от 6 символов и содержать заглавную букву.')); }
    if ($password !== $password_confirm) { wp_send_json_error(array('message' => 'Пароли не совпадают.')); }
    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'Этот Email уже зарегистрирован.'));
    }

    $check_name = gh_is_profane($name);
    if ($check_name['error']) {
        wp_send_json_error(array('message' => $check_name['message']));
    }
    $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
    $pending_data = array(
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => $role,
        'code' => $code,
        'description' => $description
    );
    set_transient('gh_pending_reg_' . md5($email), $pending_data, HOUR_IN_SECONDS);
    $subject = 'Код подтверждения | GymnasticsHub';
    $message = "Здравствуйте, $name!<br><br>Ваш код для подтверждения регистрации: <b>$code</b>";
    wp_mail($email, $subject, $message);
    wp_send_json_success(array('message' => 'Код отправлен на почту!', 'redirect' => home_url('/verify-email/?email=' . urlencode($email))));
    die();
}

// 3. AJAX Email Verification Handler
add_action('wp_ajax_gh_ajax_verify_email', 'gh_ajax_verify_email');
add_action('wp_ajax_nopriv_gh_ajax_verify_email', 'gh_ajax_verify_email');
function gh_ajax_verify_email() {
    check_ajax_referer('gh-auth-nonce', 'nonce');
    $email = sanitize_email($_POST['email']);
    $input_code = sanitize_text_field($_POST['verify_code']);
    $pending_data = get_transient('gh_pending_reg_' . md5($email));
    if (!$pending_data) { wp_send_json_error(array('message' => 'Данные не найдены.')); }
    if ($input_code === $pending_data['code']) {
        $user_id = wp_create_user($pending_data['email'], $pending_data['password'], $pending_data['email']);
        wp_update_user(array('ID' => $user_id, 'display_name' => $pending_data['name']));
        $wp_user = new WP_User($user_id);
        $wp_user->set_role($pending_data['role']);
        update_user_meta($user_id, 'gh_status', 'approved');
        if (!empty($pending_data['description'])) {
            update_user_meta($user_id, 'description', $pending_data['description']);
        }
        delete_transient('gh_pending_reg_' . md5($email));
        $creds = array('user_login' => $pending_data['email'], 'user_password' => $pending_data['password'], 'remember' => true);
        wp_signon($creds, false);
        
        $redirect_url = home_url('/profile/');
        if (!empty($_POST['redirect_to'])) {
            $redirect_url = esc_url_raw($_POST['redirect_to']);
        }
        
        wp_send_json_success(array('message' => 'Аккаунт создан!', 'redirect' => $redirect_url));
    } else { wp_send_json_error(array('message' => 'Неверный код.')); }
    die();
}

// 4. AJAX Resend Code
add_action('wp_ajax_gh_ajax_resend_code', 'gh_ajax_resend_code');
add_action('wp_ajax_nopriv_gh_ajax_resend_code', 'gh_ajax_resend_code');
function gh_ajax_resend_code() {
    check_ajax_referer('gh-auth-nonce', 'nonce');
    $email = sanitize_email($_POST['email']);
    $pending_data = get_transient('gh_pending_reg_' . md5($email));
    if (!$pending_data) { wp_send_json_error(array('message' => 'Сессия истекла.')); }
    $new_code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $pending_data['code'] = $new_code;
    set_transient('gh_pending_reg_' . md5($email), $pending_data, HOUR_IN_SECONDS);
    wp_mail($email, 'Новый код', "Ваш код: <b>$new_code</b>");
    wp_send_json_success(array('message' => 'Новый код отправлен!'));
    die();
}

// 5. AJAX Password Recovery (Send Code)
add_action('wp_ajax_gh_ajax_recovery', 'gh_ajax_recovery');
add_action('wp_ajax_nopriv_gh_ajax_recovery', 'gh_ajax_recovery');
function gh_ajax_recovery() {
    check_ajax_referer('gh-auth-nonce', 'nonce');
    $email = sanitize_email($_POST['email']);
    $user = get_user_by('email', $email);
    if (!$user) { wp_send_json_error(array('message' => 'Пользователь не найден.')); }
    
    $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    set_transient('gh_recovery_code_' . md5($email), $code, HOUR_IN_SECONDS);
    
    wp_mail($email, 'Код для сброса пароля', "Ваш код для сброса пароля: <b>$code</b>");
    wp_send_json_success(array('message' => 'Код отправлен на почту!'));
    die();
}

// 6. AJAX Reset Password (Verify Code & Set New)
add_action('wp_ajax_gh_ajax_reset_password', 'gh_ajax_reset_password');
add_action('wp_ajax_nopriv_gh_ajax_reset_password', 'gh_ajax_reset_password');
function gh_ajax_reset_password() {
    check_ajax_referer('gh-auth-nonce', 'nonce');
    $email = sanitize_email($_POST['email']);
    $code = sanitize_text_field($_POST['verify_code']);
    $new_pass = $_POST['new_password'];
    
    $saved_code = get_transient('gh_recovery_code_' . md5($email));
    if (!$saved_code) {
        wp_send_json_error(array('message' => 'Срок действия кода истек. Запросите его снова.'));
    }
    if ($code !== $saved_code) {
        wp_send_json_error(array('message' => 'Неверный код подтверждения. Проверьте почту.'));
    }
    
    $user = get_user_by('email', $email);
    wp_set_password($new_pass, $user->ID);
    delete_transient('gh_recovery_code_' . md5($email));
    
    wp_send_json_success(array('message' => 'Пароль успешно изменен!', 'redirect' => home_url('/recovery-success/')));
    die();
}

// 7. AJAX Update Profile Handler
add_action('wp_ajax_gh_ajax_update_profile', 'gh_ajax_update_profile');
function gh_ajax_update_profile() {
    if (!check_ajax_referer('gh-profile-nonce', 'nonce', false)) {
        wp_send_json_error(array('message' => 'Ошибка безопасности (nonce). Обновите страницу.'));
    }

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Вы должны быть авторизованы.'));
    }

    $user_id = get_current_user_id();
    $display_name = sanitize_text_field(wp_unslash($_POST['display_name']));
    $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';

    if (!empty($display_name)) {
        if (empty($display_name)) {
            wp_send_json_error(array('message' => 'Имя не может быть пустым.'));
        }
        $check_name = gh_is_profane($display_name);
        if ($check_name['error']) {
            wp_send_json_error(array('message' => $check_name['message']));
        }
        wp_update_user(array('ID' => $user_id, 'display_name' => $display_name));
    }

    update_user_meta($user_id, 'description', $description);

    $user = get_userdata($user_id);
    if ($user && (in_array('gh_club', $user->roles) || in_array('gh_coach', $user->roles))) {
        $country = isset($_POST['gh_country']) ? sanitize_text_field(wp_unslash($_POST['gh_country'])) : '';
        $city = isset($_POST['gh_city']) ? sanitize_text_field(wp_unslash($_POST['gh_city'])) : '';
        $phone = isset($_POST['gh_phone']) ? sanitize_text_field(wp_unslash($_POST['gh_phone'])) : '';

        update_user_meta($user_id, 'gh_country', $country);
        update_user_meta($user_id, 'gh_city', $city);
        update_user_meta($user_id, 'gh_phone', $phone);

        if (in_array('gh_club', $user->roles)) {
            $address = isset($_POST['gh_address']) ? sanitize_text_field(wp_unslash($_POST['gh_address'])) : '';
            $foundation_year = isset($_POST['gh_foundation_year']) ? sanitize_text_field(wp_unslash($_POST['gh_foundation_year'])) : '';
            $pupils_count = isset($_POST['gh_pupils_count']) ? sanitize_text_field(wp_unslash($_POST['gh_pupils_count'])) : '';
            $coaches_count = isset($_POST['gh_coaches_count']) ? sanitize_text_field(wp_unslash($_POST['gh_coaches_count'])) : '';
            
            // Separate counts for athlete ranks
            $zms_count = isset($_POST['gh_zms_count']) ? sanitize_text_field(wp_unslash($_POST['gh_zms_count'])) : '';
            $msmk_count = isset($_POST['gh_msmk_count']) ? sanitize_text_field(wp_unslash($_POST['gh_msmk_count'])) : '';
            $ms_count = isset($_POST['gh_ms_count']) ? sanitize_text_field(wp_unslash($_POST['gh_ms_count'])) : '';
            $kms_count = isset($_POST['gh_kms_count']) ? sanitize_text_field(wp_unslash($_POST['gh_kms_count'])) : '';
            $razryad_count = isset($_POST['gh_razryad_count']) ? sanitize_text_field(wp_unslash($_POST['gh_razryad_count'])) : '';

            // Calculate total as the sum of all ranks
            $total_ranked = (int)$zms_count + (int)$msmk_count + (int)$ms_count + (int)$kms_count + (int)$razryad_count;
            $ranked_athletes_count = $total_ranked > 0 ? (string)$total_ranked : '';

            $head_coach = isset($_POST['gh_head_coach']) ? sanitize_text_field(wp_unslash($_POST['gh_head_coach'])) : '';
            $coaches_links = isset($_POST['gh_coaches_links']) ? sanitize_textarea_field(wp_unslash($_POST['gh_coaches_links'])) : '';
            $gyms = isset($_POST['gh_gyms']) ? sanitize_textarea_field(wp_unslash($_POST['gh_gyms'])) : '';

            update_user_meta($user_id, 'gh_address', $address);
            update_user_meta($user_id, 'gh_foundation_year', $foundation_year);
            update_user_meta($user_id, 'gh_pupils_count', $pupils_count);
            update_user_meta($user_id, 'gh_coaches_count', $coaches_count);
            
            // Save separate ranks
            update_user_meta($user_id, 'gh_zms_count', $zms_count);
            update_user_meta($user_id, 'gh_msmk_count', $msmk_count);
            update_user_meta($user_id, 'gh_ms_count', $ms_count);
            update_user_meta($user_id, 'gh_kms_count', $kms_count);
            update_user_meta($user_id, 'gh_razryad_count', $razryad_count);
            update_user_meta($user_id, 'gh_ranked_athletes_count', $ranked_athletes_count);

            update_user_meta($user_id, 'gh_head_coach', $head_coach);
            update_user_meta($user_id, 'gh_coaches_links', $coaches_links);
            update_user_meta($user_id, 'gh_gyms', $gyms);
        }
    }

    // Handle Avatar Upload
    if (!empty($_FILES['avatar']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        
        $file = $_FILES['avatar'];
        $allowed_types = array('image/jpeg', 'image/png', 'image/webp');
        
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => 'Формат не поддерживается. Только JPG, PNG, WebP.'));
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            wp_send_json_error(array('message' => 'Файл слишком большой. Максимум 2MB.'));
        }

        // Filter for custom avatars folder
        $avatar_upload_dir = function($dirs) {
            $dirs['subdir'] = '/avatars';
            $dirs['path'] = $dirs['basedir'] . '/avatars';
            $dirs['url'] = $dirs['baseurl'] . '/avatars';
            return $dirs;
        };

        add_filter('upload_dir', $avatar_upload_dir);
        $overrides = array('test_form' => false);
        $uploaded_file = wp_handle_upload($file, $overrides);
        remove_filter('upload_dir', $avatar_upload_dir);

        if (isset($uploaded_file['file'])) {
            $webp_filename = gh_process_avatar($uploaded_file['file']);
            if ($webp_filename) {
                $avatar_url = content_url('uploads/avatars/' . $webp_filename);
                update_user_meta($user_id, 'gh_avatar', $avatar_url);
            }
        }
    }

    wp_send_json_success(array(
        'message' => 'Профиль обновлен!',
        'new_name' => $display_name,
        'new_avatar' => get_user_meta($user_id, 'gh_avatar', true),
        'new_description' => $description
    ));
    die();
}

// 8. AJAX Delete Account Handler
add_action('wp_ajax_gh_ajax_delete_account', 'gh_ajax_delete_account');
function gh_ajax_delete_account() {
    check_ajax_referer('gh-profile-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Вы должны быть авторизованы.'));
    }

    $user_id = get_current_user_id();

    // Include the necessary WordPress file for user deletion
    if (!function_exists('wp_delete_user')) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
    }

    // Delete user and all their content (reassign to null means delete posts)
    $deleted = wp_delete_user($user_id, null);

    if ($deleted) {
        wp_logout();
        wp_send_json_success(array('message' => 'Ваш аккаунт был успешно удален.', 'redirect' => home_url()));
    } else {
        wp_send_json_error(array('message' => 'Не удалось удалить пользователя. Обратитесь в поддержку.'));
    }
    die();
}
