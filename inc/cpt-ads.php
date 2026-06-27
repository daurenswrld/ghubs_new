<?php
/**
 * Custom Post Type: Advertisements (User Ads)
 */

function gh_register_ads_cpt() {
    $labels = array(
        'name'                  => 'Объявления',
        'singular_name'         => 'Объявление',
        'menu_name'             => 'Объявления',
        'all_items'             => 'Все объявления',
        'add_new'               => 'Добавить новое',
        'add_new_item'          => 'Добавить новое объявление',
        'edit_item'             => 'Редактировать объявление',
        'new_item'              => 'Новое объявление',
        'view_item'             => 'Просмотреть объявление',
        'search_items'          => 'Искать объявления',
        'not_found'             => 'Объявлений не найдено',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'ads'),
        'menu_icon'          => 'dashicons-store',
        'supports'           => array('title', 'editor', 'thumbnail', 'author'),
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'capabilities'       => array(
            'create_posts' => 'do_not_allow', // Disables "Add New" button for admin
        ),
        'map_meta_cap'       => true,
    );

    register_post_type('gh_ad', $args);
}
add_action('init', 'gh_register_ads_cpt');

/**
 * Meta Boxes for Ads
 */
function gh_ad_add_metaboxes() {
    add_meta_box('gh_ad_details', 'Детали объявления', 'gh_ad_details_callback', 'gh_ad', 'normal', 'high');
}
add_action('add_meta_boxes', 'gh_ad_add_metaboxes');

function gh_ad_details_callback($post) {
    $phone = get_post_meta($post->ID, '_ad_phone', true);
    $email = get_post_meta($post->ID, '_ad_email', true);
    $photos = get_post_meta($post->ID, '_ad_photos', true); // Array of attachment IDs
    ?>
    <table class="form-table">
        <tr>
            <th>Телефон:</th>
            <td><input type="text" name="ad_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><input type="email" name="ad_email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
        </tr>
        <?php if (!empty($photos) && is_array($photos)) : ?>
        <tr>
            <th>Фотографии:</th>
            <td>
                <div class="ad-admin-gallery" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php foreach ($photos as $photo_id) : 
                        $img_url = wp_get_attachment_image_url($photo_id, 'thumbnail');
                        if ($img_url) : ?>
                        <div class="ad-admin-photo" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                            <img src="<?php echo esc_url($img_url); ?>" alt="" style="display: block; width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <?php endif;
                    endforeach; ?>
                </div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}

function gh_save_ad_meta($post_id) {
    if (isset($_POST['ad_phone'])) update_post_meta($post_id, '_ad_phone', sanitize_text_field($_POST['ad_phone']));
    if (isset($_POST['ad_email'])) update_post_meta($post_id, '_ad_email', sanitize_text_field($_POST['ad_email']));
}
add_action('save_post_gh_ad', 'gh_save_ad_meta');

/**
 * AJAX Handler for Ad Submission
 */
function gh_handle_user_ad_submission() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Вы должны быть авторизованы.');
    }

    $title = isset($_POST['ad_title']) ? sanitize_text_field($_POST['ad_title']) : '';
    $content = isset($_POST['ad_content']) ? sanitize_textarea_field($_POST['ad_content']) : '';
    $phone = isset($_POST['ad_phone']) ? sanitize_text_field($_POST['ad_phone']) : '';
    $email = isset($_POST['ad_email']) ? sanitize_email($_POST['ad_email']) : '';
    $category = isset($_POST['ad_category']) ? intval($_POST['ad_category']) : 0;

    if (empty($title) || empty($content)) {
        wp_send_json_error('Заполните все обязательные поля.');
    }

    $post_id = wp_insert_post(array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'pending', // Sent for moderation
        'post_type'    => 'gh_ad',
        'post_author'  => get_current_user_id()
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error('Ошибка при создании объявления.');
    }

    // Save Meta
    update_post_meta($post_id, '_ad_phone', $phone);
    update_post_meta($post_id, '_ad_email', $email);

    // Handle Multiple Photos Upload
    if (!empty($_FILES['ad_photos']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Custom upload directory for ads
        $ad_dir_filter = function($dirs) {
            $dirs['subdir'] = '/ads';
            $dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
            $dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
            if (!file_exists($dirs['path'])) {
                mkdir($dirs['path'], 0755, true);
            }
            return $dirs;
        };
        add_filter('upload_dir', $ad_dir_filter);

        // Disable generation of multiple sizes (thumbnails) for ads
        $no_sizes_filter = function($sizes) {
            return array(); // No additional sizes
        };
        add_filter('intermediate_image_sizes_advanced', $no_sizes_filter);

        $files = $_FILES['ad_photos'];
        $count = count($files['name']);
        
        if ($count > 10) {
            wp_send_json_error('Максимальное количество фотографий — 10.');
        }

        $uploaded_photos = array();
        $max_size = 5 * 1024 * 1024; // 5MB

        for ($i = 0; $i < $count; $i++) {
            if ($files['name'][$i]) {
                if ($files['size'][$i] > $max_size) {
                    continue; // Skip oversized files
                }

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
                    // Convert to WebP
                    $file_path = get_attached_file($attachment_id);
                    if ($file_path) {
                        $webp_path = gh_convert_to_webp($file_path);
                        if ($webp_path && $webp_path !== $file_path) {
                            update_attached_file($attachment_id, $webp_path);
                            // Regenerate metadata
                            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $webp_path));
                        }
                    }

                    $uploaded_photos[] = $attachment_id;
                    
                    // Set first image as thumbnail
                    if ($i === 0) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }
            }
        }
        
        remove_filter('upload_dir', $ad_dir_filter);
        remove_filter('intermediate_image_sizes_advanced', $no_sizes_filter);

        if (!empty($uploaded_photos)) {
            update_post_meta($post_id, '_ad_photos', $uploaded_photos);
        }
    }

    wp_send_json_success('Объявление отправлено на модерацию.');
}
add_action('wp_ajax_gh_submit_user_ad', 'gh_handle_user_ad_submission');

/**
 * AJAX Handler for Ads Search & Filtering
 */
function gh_ajax_search_ads() {
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'new';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = array(
        'post_type'      => 'gh_ad',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => ($sort == 'old') ? 'ASC' : 'DESC'
    );

    if (!empty($search)) {
        $args['s'] = $search;
    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $phone = get_post_meta(get_the_ID(), '_ad_phone', true);
            $email = get_post_meta(get_the_ID(), '_ad_email', true);
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$thumb) $thumb = get_template_directory_uri() . '/img/card.png';
            ?>
            <a href="<?php the_permalink(); ?>" class="ad-card">
                <div class="ad-card__image">
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title(); ?>">
                    <div class="ad-card__date">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/date-white.svg" alt="">
                        <span><?php echo get_the_date('j F Y'); ?></span>
                    </div>
                    </div>
                </div>
                <div class="ad-card__content">
                    <h3 class="ad-card__title"><?php echo mb_strimwidth(get_the_title(), 0, 45, '...'); ?></h3>
                    <p class="ad-card__text"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                    <div class="ad-card__contacts">
                        <?php if ($email) : ?>
                        <div class="contact-line">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/mail-gray.svg" alt="">
                            <span><?php echo esc_html($email); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($phone) : ?>
                        <div class="contact-line">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/phone-gray.svg" alt="">
                            <span><?php echo esc_html($phone); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endwhile;
        
        echo '<div class="pagination-container" style="grid-column: 1/-1; margin-top: 40px; display: flex; justify-content: center;">';
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => $paged,
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
            'base' => '#%#%',
            'format' => '?paged=%#%',
        ));
        echo '</div>';

        wp_reset_postdata();
    else : ?>
        <div class="empty-state" style="grid-column: 1/-1; padding: 80px 20px; text-align: center; background: #fff; border-radius: 32px; border: 1px dashed #e0e0e0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px;">
            <div class="empty-state__icon" style="width: 80px; height: 80px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
            </div>
            <div class="empty-state__content">
                <h3 style="font-size: 24px; color: #1a1a1a; font-weight: 600; margin-bottom: 8px;">Ничего не найдено</h3>
                <p style="color: #666; font-size: 16px; max-width: 400px; margin: 0 auto 24px; line-height: 1.5;">Попробуйте изменить запрос или сбросить фильтры.</p>
            </div>
        </div>
    <?php endif;

    $html = ob_get_clean();
    wp_send_json_success($html);
}
add_action('wp_ajax_gh_search_ads', 'gh_ajax_search_ads');
add_action('wp_ajax_nopriv_gh_search_ads', 'gh_ajax_search_ads');
