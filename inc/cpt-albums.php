<?php
/**
 * Register Custom Post Type for Albums (Gallery)
 */
function gh_register_album_cpt() {
    $labels = array(
        'name'                  => 'Альбомы',
        'singular_name'         => 'Альбом',
        'menu_name'             => 'Альбомы',
        'name_admin_bar'        => 'Альбом',
        'add_new'               => 'Добавить новый',
        'add_new_item'          => 'Добавить новый Альбом',
        'new_item'              => 'Новый Альбом',
        'edit_item'             => 'Редактировать Альбом',
        'view_item'             => 'Просмотреть Альбом',
        'all_items'             => 'Все Альбомы',
        'search_items'          => 'Искать Альбомы',
        'not_found'             => 'Альбомы не найдены.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'gallery'),
        'capability_type'    => 'post',
        'capabilities'       => array(
            'create_posts' => 'do_not_allow', // Disables "Add New" in admin
        ),
        'map_meta_cap'       => true,
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-format-gallery',
        'supports'           => array('title', 'editor', 'thumbnail', 'author'),
    );

    register_post_type('gallery_album', $args);
}
add_action('init', 'gh_register_album_cpt');

/**
 * Register Custom Post Status for Rejected Albums
 */
function gh_register_album_status() {
    register_post_status('rejected', array(
        'label'                     => 'Отклонено',
        'public'                    => false,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Отклонено <span class="count">(%s)</span>', 'Отклонено <span class="count">(%s)</span>'),
    ));
}
add_action('init', 'gh_register_album_status');

/**
 * Handle AJAX submission of a new Album
 */
add_action('wp_ajax_submit_album', 'gh_ajax_submit_album');

function gh_ajax_submit_album() {
    if ( !is_user_logged_in() ) {
        wp_send_json_error('Только авторизованные пользователи могут добавлять альбомы.');
    }

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gh-auth-nonce')) {
        wp_send_json_error('Ошибка безопасности. Пожалуйста, обновите страницу.');
    }

    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    if (empty($title)) {
        wp_send_json_error('Название альбома обязательно.');
    }

    // Prepare post data
    $post_data = array(
        'post_title'   => $title,
        'post_status'  => 'pending', // Requires approval
        'post_type'    => 'gallery_album',
        'post_author'  => is_user_logged_in() ? get_current_user_id() : 0,
    );

    // Insert post
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error('Не удалось сохранить альбом.');
    }

    // Save metadata
    $meta_fields = array(
        'drive_link', 'country', 'city', 'location_name', 
        'dates', 'category', 'phone', 'email', 'whatsapp', 
        'instagram', 'telegram'
    );

    foreach ($meta_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_gh_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Handle photo uploads if any
    if (!empty($_FILES['photos'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Custom upload directory for albums
        $album_dir_filter = function($dirs) {
            $dirs['subdir'] = '/gallery_albums';
            $dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
            $dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
            if (!file_exists($dirs['path'])) {
                mkdir($dirs['path'], 0755, true);
            }
            return $dirs;
        };
        add_filter('upload_dir', $album_dir_filter);

        // Disable generating multiple thumbnails to save disk space
        $disable_thumbnails = function($sizes) { return array(); };
        add_filter('intermediate_image_sizes_advanced', $disable_thumbnails);

        $files = $_FILES['photos'];
        $count = count($files['name']);
        
        if ($count > 6) {
            $count = 6;
        }

        $uploaded_images = array();

        for ($i = 0; $i < $count; $i++) {
            if ($files['name'][$i]) {
                $file = array(
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i]
                );

                $_FILES = array("upload_file" => $file);
                $attachment_id = media_handle_upload("upload_file", $post_id);

                if (!is_wp_error($attachment_id)) {
                    $uploaded_images[] = $attachment_id;
                    if ($i === 0) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }
            }
        }
        
        // Remove the filters after uploads
        remove_filter('upload_dir', $album_dir_filter);
        remove_filter('intermediate_image_sizes_advanced', $disable_thumbnails);

        // Clear countries cache to show new country if added
        delete_transient('gh_active_countries');

        if (!empty($uploaded_images)) {
            update_post_meta($post_id, '_gh_album_photos', array_unique($uploaded_images));
        }
    }

    wp_send_json_success('Альбом успешно отправлен на модерацию.');
}

/**
 * Add Meta Box to view Album details in admin
 */
function gh_add_album_meta_boxes() {
    add_meta_box(
        'gh_album_details',
        'Данные альбома',
        'gh_render_album_meta_box',
        'gallery_album',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'gh_add_album_meta_boxes');

function gh_render_album_meta_box($post) {
    $drive_link    = get_post_meta($post->ID, '_gh_drive_link', true);
    $country       = get_post_meta($post->ID, '_gh_country', true);
    $city          = get_post_meta($post->ID, '_gh_city', true);
    $location_name = get_post_meta($post->ID, '_gh_location_name', true);
    $dates         = get_post_meta($post->ID, '_gh_dates', true);
    $category      = get_post_meta($post->ID, '_gh_category', true);
    $phone         = get_post_meta($post->ID, '_gh_phone', true);
    $email         = get_post_meta($post->ID, '_gh_email', true);
    $whatsapp      = get_post_meta($post->ID, '_gh_whatsapp', true);
    $instagram     = get_post_meta($post->ID, '_gh_instagram', true);
    $telegram      = get_post_meta($post->ID, '_gh_telegram', true);
    $photos        = get_post_meta($post->ID, '_gh_album_photos', true);

    echo '<table class="form-table">';
    echo '<tr><th>Категория:</th><td>' . esc_html($category) . '</td></tr>';
    echo '<tr><th>Страна:</th><td>' . esc_html($country) . '</td></tr>';
    echo '<tr><th>Город:</th><td>' . esc_html($city) . '</td></tr>';
    echo '<tr><th>Место:</th><td>' . esc_html($location_name) . '</td></tr>';
    echo '<tr><th>Даты:</th><td>' . esc_html($dates) . '</td></tr>';
    echo '<tr><th>Ссылка на диск:</th><td>' . ($drive_link ? '<a href="'.esc_url($drive_link).'" target="_blank">'.esc_html($drive_link).'</a>' : '—') . '</td></tr>';
    echo '<tr><th>Телефон:</th><td>' . esc_html($phone) . '</td></tr>';
    echo '<tr><th>Email:</th><td>' . esc_html($email) . '</td></tr>';
    echo '<tr><th>WhatsApp:</th><td>' . esc_html($whatsapp) . '</td></tr>';
    echo '<tr><th>Instagram:</th><td>' . esc_html($instagram) . '</td></tr>';
    echo '<tr><th>Telegram:</th><td>' . esc_html($telegram) . '</td></tr>';
    echo '</table>';

    if (!empty($photos) && is_array($photos)) {
        echo '<h4>Загруженные фото:</h4>';
        echo '<div style="display: flex; gap: 10px; flex-wrap: wrap;">';
        foreach ($photos as $photo_id) {
            echo wp_get_attachment_image($photo_id, 'thumbnail', false, array('style' => 'width: 100px; height: 100px; object-fit: cover;'));
        }
        echo '</div>';
    }

    // Action buttons
    $preview_link = get_preview_post_link($post);
    $delete_link = get_delete_post_link($post->ID);
    
    echo '<div style="margin-top:20px; padding-top:15px; border-top:1px solid #ddd; display:flex; gap:10px; align-items:center;">';
    echo '<a href="'.esc_url($preview_link).'" target="_blank" class="button button-secondary">Посмотреть на сайте</a>';
    
    if ($post->post_status !== 'publish') {
        echo '<button type="button" class="button button-primary" onclick="document.getElementById(\'post_status\').value=\'publish\'; document.getElementById(\'publish\').click();">Подтвердить (Опубликовать)</button>';
    }
    
    if ($post->post_status !== 'rejected' && $post->post_status !== 'publish') {
        echo '<button type="button" class="button" style="color:#a00; border-color:#a00;" onclick="if(confirm(\'Отклонить этот альбом?\')){document.getElementById(\'post_status\').value=\'rejected\'; document.getElementById(\'publish\').click();}">Отклонить</button>';
    }
    
    if ($delete_link) {
        echo '<a href="'.esc_url($delete_link).'" class="button" style="color:#666; border-color:transparent; background:transparent; margin-left:auto;">Удалить навсегда</a>';
    }
    echo '</div>';
}
