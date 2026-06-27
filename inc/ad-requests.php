<?php
/**
 * Advertisement Requests Management
 */

// 1. Register Custom Post Type for Ad Requests
function gh_register_ad_requests_cpt() {
    $labels = array(
        'name'               => 'Заявки на рекламу',
        'singular_name'      => 'Заявка на рекламу',
        'menu_name'          => 'Заявки на рекламу',
        'add_new'            => 'Добавить новую',
        'add_new_item'       => 'Добавить новую заявку',
        'edit_item'          => 'Просмотр заявки',
        'new_item'           => 'Новая заявка',
        'view_item'          => 'Посмотреть заявку',
        'search_items'       => 'Искать заявки',
        'not_found'          => 'Заявок не найдено',
        'not_found_in_trash' => 'В корзине заявок нет'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-megaphone',
        'capability_type'     => 'post',
        'capabilities' => array(
            'create_posts' => 'do_not_allow', // Disable manual creation
        ),
        'map_meta_cap'        => true,
        'hierarchical'        => false,
        'supports'            => array('title'),
        'has_archive'         => false,
    );

    register_post_type('ad_request', $args);
}
add_action('init', 'gh_register_ad_requests_cpt');

// 2. Meta Box for Request Details
function gh_ad_request_add_metabox() {
    add_meta_box(
        'ad_request_details',
        'Данные заявки',
        'gh_ad_request_metabox_callback',
        'ad_request',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'gh_ad_request_add_metabox');

function gh_ad_request_metabox_callback($post) {
    $name   = get_post_meta($post->ID, '_ad_user_name', true);
    $phone  = get_post_meta($post->ID, '_ad_user_phone', true);
    $email  = get_post_meta($post->ID, '_ad_user_email', true);
    $url    = get_post_meta($post->ID, '_ad_image_url', true);
    $link   = get_post_meta($post->ID, '_ad_link', true);
    
    // Publishing settings
    $status = get_post_meta($post->ID, '_ad_publish_status', true);
    $slot   = get_post_meta($post->ID, '_ad_publish_slot', true);

    $types = array(
        'b1' => 'Баннер 1 (1662x1056)',
        'b2' => 'Баннер 2 (1856x704)',
        'b3' => 'Баннер 3 (652x908)',
        'b4' => 'Баннер 4 (909x1455)'
    );
    $type_label = isset($types[$type]) ? $types[$type] : $type;

    ?>
    <div class="ad-request-admin-flex" style="display: flex; gap: 30px;">
        <div class="ad-request-info" style="flex: 1;">
            <h3>Данные клиента</h3>
            <table class="form-table">
                <tr><th>Имя:</th><td><strong><?php echo esc_html($name); ?></strong></td></tr>
                <tr><th>Телефон:</th><td><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></td></tr>
                <tr><th>Email:</th><td><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></td></tr>
                <tr><th>Тип в заявке:</th><td><?php echo esc_html($type_label); ?></td></tr>
                <tr><th>Ссылка (URL):</th><td><?php echo $link ? '<a href="'.esc_url($link).'" target="_blank">'.esc_html($link).'</a>' : '—'; ?></td></tr>
            </table>
            
            <h3 style="margin-top: 30px;">Макет</h3>
            <?php if ($url) : ?>
                <img src="<?php echo esc_url($url); ?>" style="max-width: 100%; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);"><br>
                <a href="<?php echo esc_url($url); ?>" target="_blank" class="button" style="margin-top: 10px;">Открыть макет</a>
            <?php endif; ?>
        </div>

        <div class="ad-request-publishing" style="flex: 1; background: #f0f0f0; padding: 20px; border-radius: 10px; border: 1px solid #ccc;">
            <h3 style="margin-top: 0;">Управление публикацией</h3>
            <p>Выберите слот, если хотите вывести этот баннер на сайт.</p>
            
            <table class="form-table">
                <tr>
                    <th>Статус:</th>
                    <td>
                        <select name="ad_publish_status" style="width: 100%;">
                            <option value="pending" <?php selected($status, 'pending'); ?>>Новая заявка</option>
                            <option value="active" <?php selected($status, 'active'); ?>>Опубликовано (Активен)</option>
                            <option value="archived" <?php selected($status, 'archived'); ?>>В архиве</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Слот на сайте:</th>
                    <td>
                        <select name="ad_publish_slot" style="width: 100%;">
                            <option value="">Не выбрано</option>
                            <option value="1" <?php selected($slot, '1'); ?>>Слот 1 (Секция ADS)  макс. 2 баннера</option>
                            <option value="2" <?php selected($slot, '2'); ?>>Слот 2 (Promo Slider) без ограничения</option>
                            <option value="3" <?php selected($slot, '3'); ?>>Слот 3 (Events Grid) макс. 1 баннер</option>
                            <option value="4" <?php selected($slot, '4'); ?>>Слот 4 (Banners ADS) макс. 4 баннера</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="description" style="color: #d63638;">* При выборе слота и статуса "Активен", этот баннер заменит текущий на сайте.</p>
        </div>
    </div>
    <?php
}

// 2.1 Save Publishing Data
function gh_save_ad_request_publishing_data($post_id) {
    if (isset($_POST['ad_publish_status'])) {
        update_post_meta($post_id, '_ad_publish_status', sanitize_text_field($_POST['ad_publish_status']));
    }
    if (isset($_POST['ad_publish_slot'])) {
        update_post_meta($post_id, '_ad_publish_slot', sanitize_text_field($_POST['ad_publish_slot']));
    }
}
add_action('save_post_ad_request', 'gh_save_ad_request_publishing_data');

// 3. Add columns to Admin List
function gh_set_ad_requests_columns($columns) {
    unset($columns['date']);
    $columns['ad_name'] = 'Имя Фамилия';
    $columns['ad_type'] = 'Заявка на тип';
    $columns['ad_status_slot'] = 'Статус / Слот';
    $columns['ad_image'] = 'Макет';
    $columns['date'] = 'Дата';
    return $columns;
}
add_filter('manage_ad_request_posts_columns', 'gh_set_ad_requests_columns');

function gh_display_ad_requests_columns($column, $post_id) {
    switch ($column) {
        case 'ad_name':
            echo '<strong>' . esc_html(get_post_meta($post_id, '_ad_user_name', true)) . '</strong><br>';
            echo '<small>' . esc_html(get_post_meta($post_id, '_ad_user_phone', true)) . '</small>';
            break;
        case 'ad_type':
            $types = array('b1'=>'Б1','b2'=>'Б2','b3'=>'Б3','b4'=>'Б4');
            $type = get_post_meta($post_id, '_ad_type', true);
            echo isset($types[$type]) ? $types[$type] : $type;
            break;
        case 'ad_status_slot':
            $status = get_post_meta($post_id, '_ad_publish_status', true);
            $slot = get_post_meta($post_id, '_ad_publish_slot', true);
            
            if ($status === 'active') {
                echo '<span style="color:#2271b1; font-weight:700;">● Опубликован</span>';
                if ($slot) echo '<br>Слот: ' . $slot;
            } elseif ($status === 'archived') {
                echo '<span style="color:#666;">В архиве</span>';
            } else {
                echo '<span style="color:#d63638;">Новая заявка</span>';
            }
            break;
        case 'ad_image':
            $url = get_post_meta($post_id, '_ad_image_url', true);
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank"><img src="' . esc_url($url) . '" style="width:50px; height:50px; object-fit:cover; border-radius:5px;"></a>';
            }
            break;
    }
}
add_action('manage_ad_request_posts_custom_column', 'gh_display_ad_requests_columns', 10, 2);

/**
 * Helper to get active ads for a slot
 */
function gh_get_active_ads($slot, $limit = -1) {
    $args = array(
        'post_type'      => 'ad_request',
        'posts_per_page' => $limit,
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'   => '_ad_publish_status',
                'value' => 'active',
            ),
            array(
                'key'   => '_ad_publish_slot',
                'value' => $slot,
            )
        ),
        'orderby' => 'date',
        'order'   => 'DESC'
    );
    $query = new WP_Query($args);
    $ads = array();
    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $image = get_post_meta($post->ID, '_ad_image_url', true);
            $link  = get_post_meta($post->ID, '_ad_link', true);
            if ($image) {
                $ads[] = array(
                    'image' => $image,
                    'link'  => $link ? $link : '#!'
                );
            }
        }
    }
    return $ads;
}

/**
 * Filter to change upload directory for ad requests
 */
function gh_ad_requests_upload_dir($dirs) {
    $dirs['subdir'] = '/ad-requests';
    $dirs['path']   = $dirs['basedir'] . '/ad-requests';
    $dirs['url']    = $dirs['baseurl'] . '/ad-requests';
    return $dirs;
}

// 3. AJAX Handler for Submission
add_action('wp_ajax_submit_ad_request', 'gh_handle_ad_request_submission');
add_action('wp_ajax_nopriv_submit_ad_request', 'gh_handle_ad_request_submission');

function gh_handle_ad_request_submission() {
    $name  = sanitize_text_field($_POST['ad_user_name']);
    $phone = sanitize_text_field($_POST['ad_user_phone']);
    $email = sanitize_email($_POST['ad_user_email']);
    $type  = sanitize_text_field($_POST['ad_type']);
    $link  = isset($_POST['ad_link']) ? esc_url_raw($_POST['ad_link']) : '';

    if (empty($name) || empty($phone) || empty($email) || empty($type) || empty($_FILES['ad_image']['name'])) {
        wp_send_json_error(array('message' => 'Пожалуйста, заполните все поля и прикрепите макет.'));
    }

    // Create the post
    $post_id = wp_insert_post(array(
        'post_type'   => 'ad_request',
        'post_title'  => 'Заявка от ' . $name,
        'post_status' => 'publish',
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => 'Ошибка при сохранении заявки.'));
    }

    // Save Meta
    update_post_meta($post_id, '_ad_user_name', $name);
    update_post_meta($post_id, '_ad_user_phone', $phone);
    update_post_meta($post_id, '_ad_user_email', $email);
    update_post_meta($post_id, '_ad_type', $type);
    if ($link) {
        update_post_meta($post_id, '_ad_link', $link);
    }

    // Handle Custom File Upload (Separate folder, not in Media Library)
    if (!empty($_FILES['ad_image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        // Apply custom directory filter
        add_filter('upload_dir', 'gh_ad_requests_upload_dir');
        
        $uploaded_file = wp_handle_upload($_FILES['ad_image'], array('test_form' => false));
        
        // Remove filter immediately after
        remove_filter('upload_dir', 'gh_ad_requests_upload_dir');

        if (isset($uploaded_file['url'])) {
            update_post_meta($post_id, '_ad_image_url', $uploaded_file['url']);
        }
    }

    wp_send_json_success(array('message' => 'Заявка успешно отправлена!'));
}
