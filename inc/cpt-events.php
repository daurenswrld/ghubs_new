<?php
/**
 * Custom Post Type: Events
 */

function gh_register_events_cpt() {
    $labels = array(
        'name'                  => 'Мероприятия',
        'singular_name'         => 'Мероприятие',
        'menu_name'             => 'Мероприятия',
        'all_items'             => 'Все мероприятия',
        'add_new'               => 'Добавить новое',
        'add_new_item'          => 'Добавить новое мероприятие',
        'edit_item'             => 'Редактировать мероприятие',
        'new_item'              => 'Новое мероприятие',
        'view_item'             => 'Просмотреть мероприятие',
        'search_items'          => 'Искать мероприятия',
        'not_found'             => 'Мероприятий не найдено',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'events'),
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'capabilities'       => array(
            'create_posts' => 'do_not_allow', // Disable adding from admin
        ),
        'map_meta_cap'       => true,
    );

    register_post_type('gh_event', $args);

    // Taxonomy: Event Type (Tournament, Camp, Seminar)
    register_taxonomy('event_type', 'gh_event', array(
        'labels' => array(
            'name' => 'Типы мероприятий',
            'singular_name' => 'Тип мероприятия',
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'event-type'),
    ));




}
add_action('init', 'gh_register_events_cpt');

/**
 * Meta Boxes for Events
 */
function gh_event_add_metaboxes() {
    add_meta_box('gh_event_details', 'Детали мероприятия', 'gh_event_details_callback', 'gh_event', 'normal', 'high');
}
add_action('add_meta_boxes', 'gh_event_add_metaboxes');

function gh_event_details_callback($post) {
    $start_date = get_post_meta($post->ID, '_event_start_date', true);
    $end_date   = get_post_meta($post->ID, '_event_end_date', true);
    $location   = get_post_meta($post->ID, '_event_location_text', true);
    $price      = get_post_meta($post->ID, '_event_price', true);
    $reg_url    = get_post_meta($post->ID, '_event_reg_url', true);
    $organizer  = get_post_meta($post->ID, '_event_organizer', true);
    $photos     = get_post_meta($post->ID, '_event_photos', true);
    $docs       = get_post_meta($post->ID, '_event_docs', true);
    $whatsapp   = get_post_meta($post->ID, '_event_whatsapp', true);
    $telegram   = get_post_meta($post->ID, '_event_telegram', true);
    $instagram  = get_post_meta($post->ID, '_event_instagram', true);
    ?>
    <table class="form-table">
        <tr>
            <th>Дата начала:</th>
            <td><input type="date" name="event_start_date" value="<?php echo esc_attr($start_date); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th>Дата окончания:</th>
            <td><input type="date" name="event_end_date" value="<?php echo esc_attr($end_date); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th>Место проведения (текстом):</th>
            <td><input type="text" name="event_location_text" value="<?php echo esc_attr($location); ?>" class="regular-text" placeholder="Например: Дворец спорта, Алматы"></td>
        </tr>
        <tr>
            <th>Стоимость:</th>
            <td><input type="text" name="event_price" value="<?php echo esc_attr($price); ?>" class="regular-text" placeholder="Например: От 5000 ₸"></td>
        </tr>
        <tr>
            <th>Ссылка на регистрацию:</th>
            <td><input type="url" name="event_reg_url" value="<?php echo esc_url($reg_url); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th>WhatsApp:</th>
            <td><input type="text" name="event_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" class="regular-text" placeholder="77071234567"></td>
        </tr>
        <tr>
            <th>Telegram:</th>
            <td><input type="text" name="event_telegram" value="<?php echo esc_attr($telegram); ?>" class="regular-text" placeholder="username"></td>
        </tr>
        <tr>
            <th>Instagram:</th>
            <td><input type="text" name="event_instagram" value="<?php echo esc_attr($instagram); ?>" class="regular-text" placeholder="username"></td>
        </tr>
        <tr>
            <th>Организатор:</th>
            <td><input type="text" name="event_organizer" value="<?php echo esc_attr($organizer); ?>" class="regular-text"></td>
        </tr>
    </table>
    
    <div class="event-photos-admin" style="margin-top: 20px;">
        <h4>Документы (Положение):</h4>
        <div class="docs-preview" style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">
            <?php 
            $docs_array = is_array($docs) ? $docs : (!empty($docs) ? array($docs) : array());
            if (!empty($docs_array)) : 
                foreach ($docs_array as $id) : ?>
                <div class="doc-item" style="margin-bottom: 5px;">
                    <span class="dashicons dashicons-media-document" style="vertical-align: middle; margin-right: 5px;"></span>
                    <a href="<?php echo wp_get_attachment_url($id); ?>" target="_blank" style="vertical-align: middle;"><?php echo get_the_title($id); ?></a>
                </div>
                <?php endforeach; 
            else : ?>
                <p style="color: #999; margin: 0;">Документы не прикреплены.</p>
            <?php endif; ?>
        </div>

        <h4>Фотографии мероприятия:</h4>
        <div class="photo-preview" style="display: flex; flex-wrap: wrap; gap: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">
            <?php 
            $photos_array = is_array($photos) ? $photos : (!empty($photos) ? array($photos) : array());
            if (!empty($photos_array)) : 
                foreach ($photos_array as $id) : ?>
                <div class="photo-item" style="border: 1px solid #ccc; background: #fff; padding: 2px;">
                    <?php echo wp_get_attachment_image($id, array(100, 100)); ?>
                </div>
                <?php endforeach; 
            else : ?>
                <p style="color: #999; margin: 0;">Фотографии не загружены.</p>
            <?php endif; ?>
        </div>
        <p class="description" style="margin-top: 10px;">Файлы загружаются пользователем через фронтенд-форму подачи мероприятия.</p>
    </div>
    <?php
}

function gh_save_event_meta($post_id) {
    if (isset($_POST['event_start_date'])) update_post_meta($post_id, '_event_start_date', sanitize_text_field($_POST['event_start_date']));
    if (isset($_POST['event_end_date']))   update_post_meta($post_id, '_event_end_date', sanitize_text_field($_POST['event_end_date']));
    if (isset($_POST['event_location_text'])) update_post_meta($post_id, '_event_location_text', sanitize_text_field($_POST['event_location_text']));
    if (isset($_POST['event_price']))      update_post_meta($post_id, '_event_price', sanitize_text_field($_POST['event_price']));
    if (isset($_POST['event_reg_url']))    update_post_meta($post_id, '_event_reg_url', esc_url_raw($_POST['event_reg_url']));
    if (isset($_POST['event_whatsapp']))   update_post_meta($post_id, '_event_whatsapp', sanitize_text_field($_POST['event_whatsapp']));
    if (isset($_POST['event_telegram']))   update_post_meta($post_id, '_event_telegram', sanitize_text_field($_POST['event_telegram']));
    if (isset($_POST['event_instagram']))  update_post_meta($post_id, '_event_instagram', sanitize_text_field($_POST['event_instagram']));
    if (isset($_POST['event_organizer']))  update_post_meta($post_id, '_event_organizer', sanitize_text_field($_POST['event_organizer']));
}
add_action('save_post_gh_event', 'gh_save_event_meta');

/**
 * Helper: Format Event Date Range
 */
function gh_format_event_date($start, $end) {
    if (empty($start)) return 'Дата не указана';
    
    $start_ts = strtotime($start);
    if (empty($end) || $start === $end) {
        return date_i18n('j F Y', $start_ts);
    }
    
    $end_ts = strtotime($end);
    
    // If same month and year: "14–16 марта 2026"
    if (date('m Y', $start_ts) === date('m Y', $end_ts)) {
        return date('j', $start_ts) . '–' . date_i18n('j F Y', $end_ts);
    }
    
    // Different months: "28 марта – 2 апреля 2026"
    return date_i18n('j F', $start_ts) . ' – ' . date_i18n('j F Y', $end_ts);
}

/**
 * AJAX: Filter Events
 */
function gh_filter_events() {
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'near';
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'all';
    $locations_raw = isset($_POST['locations']) ? $_POST['locations'] : '[]';
    $locations = json_decode(stripslashes($locations_raw), true);
    $date_start = isset($_POST['date_start']) ? sanitize_text_field($_POST['date_start']) : '';
    $date_end = isset($_POST['date_end']) ? sanitize_text_field($_POST['date_end']) : '';
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';

    $args = array(
        'post_type'      => 'gh_event',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    // Meta Query for Country/City
    $meta_query = array();
    if (!empty($country) || !empty($city)) {
        $meta_query['relation'] = 'AND';
        if (!empty($country)) {
            $meta_query[] = array(
                'key'     => '_event_location_country',
                'value'   => $country,
                'compare' => 'LIKE'
            );
        }
        if (!empty($city)) {
            $meta_query[] = array(
                'key'     => '_event_location_city',
                'value'   => $city,
                'compare' => 'LIKE'
            );
        }
    }

    // Taxonomy Filters
    $tax_query = array();
    if ($type !== 'all') {
        $tax_query[] = array(
            'taxonomy' => 'event_type',
            'field'    => 'slug',
            'terms'    => $type,
        );
        $args['tax_query'] = $tax_query;
    }

    if (!empty($locations)) {
        // filter by country using meta
        $meta_query[] = array(
            'key'     => '_event_location_country',
            'value'   => $locations,
            'compare' => 'IN'
        );
    }

    // Date Filter
    if (!empty($date_start) || !empty($date_end)) {
        if (!empty($date_start) && !empty($date_end)) {
            // Both start and end dates are specified
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key'     => '_event_start_date',
                    'value'   => $date_end,
                    'compare' => '<=',
                    'type'    => 'DATE'
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_event_end_date',
                        'value'   => $date_start,
                        'compare' => '>=',
                        'type'    => 'DATE'
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key'     => '_event_end_date',
                            'value'   => '',
                            'compare' => '='
                        ),
                        array(
                            'key'     => '_event_start_date',
                            'value'   => $date_start,
                            'compare' => '>=',
                            'type'    => 'DATE'
                        )
                    )
                )
            );
        } elseif (!empty($date_start)) {
            // Only start date specified (show all future/current events from this date)
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_event_end_date',
                    'value'   => $date_start,
                    'compare' => '>=',
                    'type'    => 'DATE'
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key'     => '_event_end_date',
                        'value'   => '',
                        'compare' => '='
                    ),
                    array(
                        'key'     => '_event_start_date',
                        'value'   => $date_start,
                        'compare' => '>=',
                        'type'    => 'DATE'
                    )
                )
            );
        } elseif (!empty($date_end)) {
            // Only end date specified (show all events that started before/on this date)
            $meta_query[] = array(
                'key'     => '_event_start_date',
                'value'   => $date_end,
                'compare' => '<=',
                'type'    => 'DATE'
            );
        }
    }

    // Sorting
    if ($sort === 'near') {
        $args['meta_key'] = '_event_start_date';
        $args['orderby'] = 'meta_value';
        $args['order'] = 'ASC';
        
        // If no specific date is selected, show events from today onwards
        if (empty($date_start) && empty($date_end)) {
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_event_end_date',
                    'value'   => date('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE'
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key'     => '_event_end_date',
                        'value'   => '',
                        'compare' => '='
                    ),
                    array(
                        'key'     => '_event_start_date',
                        'value'   => date('Y-m-d'),
                        'compare' => '>=',
                        'type'    => 'DATE'
                    )
                )
            );
        }
    } else {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    if (!empty($search)) {
        $args['s'] = $search;
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) :
        $active_ads = gh_get_active_ads('3', -1);
        $ad_index = 0;
        $counter = 0;
        $sorted_posts = gh_sort_events_by_type($query->posts);
        global $post;
        foreach ($sorted_posts as $post) : setup_postdata($post);
            $counter++;
            $start_date = get_post_meta(get_the_ID(), '_event_start_date', true);
            $end_date   = get_post_meta(get_the_ID(), '_event_end_date', true);
            $city       = get_post_meta(get_the_ID(), '_event_location_city', true);
            $country    = get_post_meta(get_the_ID(), '_event_location_country', true);
            $location_display = trim(($city ? $city : '') . ($city && $country ? ', ' : '') . ($country ? $country : ''));
            $types      = get_the_terms(get_the_ID(), 'event_type');
            $type_name  = !empty($types) ? $types[0]->name : 'Мероприятие';
            ?>
            <div class="event-card">
                <div class="event-card__image">
                    <?php if (has_post_thumbnail()) : the_post_thumbnail('large'); else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/img/card.png" alt="Event">
                    <?php endif; ?>
                </div>
                <div class="event-card__content">
                    <div class="event-card__top">
                        <h3 class="event-card__title"><?php the_title(); ?></h3>
                        <div class="event-card__meta">
                            <div class="meta-item">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/cup.svg" alt="Type" class="meta-icon">
                                <span><?php echo esc_html($type_name); ?></span>
                            </div>
                        </div>
                        <div class="meta-item location">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/geo.svg" alt="Location" class="meta-icon">
                            <span><?php echo esc_html($location_display); ?></span>
                        </div>
                    </div>
                    <div class="event-card__bottom">
                        <div class="meta-item date">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/date.svg" alt="Date" class="meta-icon">
                            <span><?php echo gh_format_event_date($start_date, $end_date); ?></span>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="event-card__arrow">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right.svg" alt="Details">
                        </a>
                    </div>
                </div>
            </div>
            <?php
            if ($counter % 4 == 0) {
                $ad_card = null;
                if (!empty($active_ads)) {
                    $ad_card = $active_ads[$ad_index % count($active_ads)];
                    $ad_index++;
                }
                $ad_img = $ad_card ? $ad_card['image'] : get_template_directory_uri() . '/img/ad-test.png';
                $ad_link = $ad_card ? $ad_card['link'] : '#!';
                $is_empty_ad = empty($ad_card);
                ?>
                <div class="event-ad-card">
                    <a href="<?php echo esc_url($ad_link); ?>" <?php echo ($ad_link !== '#!') ? 'target="_blank"' : ''; ?> style="display: block; height: 100%; min-height: 400px; text-decoration: none;">
                        <div class="event-ad-card__bg">
                            <img src="<?php echo esc_url($ad_img); ?>" alt="Advertisement">
                        </div>
                        <?php if ($is_empty_ad) : ?>
                        <div class="event-ad-card__content">
                            <h3 class="ad-title">JOIN OUR <br>COMMUNITY</h3>
                            <p class="ad-subtitle">Everything about gymnastics</p>
                        </div>
                        <?php endif; ?>
                    </a>
                </div>
                <?php
            }
            ?>
        <?php 
        endforeach; 
        wp_reset_postdata();

        // Output remaining ads at the end of the events list
        if (!empty($active_ads) && $ad_index < count($active_ads)) {
            for ($i = $ad_index; $i < count($active_ads); $i++) {
                $ad_card = $active_ads[$i];
                $ad_img = $ad_card['image'];
                $ad_link = $ad_card['link'];
                ?>
                <div class="event-ad-card">
                    <a href="<?php echo esc_url($ad_link); ?>" <?php echo ($ad_link !== '#!') ? 'target="_blank"' : ''; ?> style="display: block; height: 100%; min-height: 400px; text-decoration: none;">
                        <div class="event-ad-card__bg">
                            <img src="<?php echo esc_url($ad_img); ?>" alt="Advertisement">
                        </div>
                    </a>
                </div>
                <?php
            }
        }
        ?>
    else : ?>
        <div class="empty-state" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; grid-column: 1 / -1; width: 100%;">
            <div class="empty-state__icon" style="width: 80px; height: 80px; background: #F3F4F6; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <img src="<?php echo get_template_directory_uri(); ?>/img/search-gray.svg" alt="Not found" style="width: 40px; height: 40px; opacity: 0.5;">
            </div>
            <h3 class="empty-state__title" style="font-size: 24px; font-weight: 600; margin-bottom: 12px; color: #111;">Мероприятий не найдено</h3>
            <p class="empty-state__text" style="color: #666; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.5;">По вашему запросу ничего не найдено. Попробуйте изменить параметры поиска или добавить новое мероприятие.</p>
            <div class="empty-state__actions" style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <button type="button" onclick="window.location.href='<?php echo get_post_type_archive_link('gh_event'); ?>'" class="btn" style="border: 1px solid #e5e7eb; background: transparent; color: #111;">Сбросить фильтры</button>
                <a href="<?php echo site_url('/add-event/'); ?>" class="btn btn--black">Добавить событие</a>
            </div>
        </div>
    <?php endif;

    $html = ob_get_clean();
    wp_send_json_success($html);
}
add_action('wp_ajax_gh_filter_events', 'gh_filter_events');
add_action('wp_ajax_nopriv_gh_filter_events', 'gh_filter_events');

/**
 * AJAX: Submit Event
 */
function gh_submit_event() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Вы должны быть авторизованы.');
    }

    $title       = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $type        = isset($_POST['event_type']) ? sanitize_text_field($_POST['event_type']) : '';
    $country     = isset($_POST['location_country']) ? sanitize_text_field($_POST['location_country']) : '';
    $city        = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $place       = isset($_POST['place']) ? sanitize_text_field($_POST['place']) : '';
    $start_date  = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
    $end_date    = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
    $price       = isset($_POST['price']) ? sanitize_text_field($_POST['price']) : '';
    $reg_url     = isset($_POST['reg_url']) ? esc_url_raw($_POST['reg_url']) : '';
    $whatsapp    = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : '';
    $telegram    = isset($_POST['telegram']) ? sanitize_text_field($_POST['telegram']) : '';
    $instagram   = isset($_POST['instagram']) ? sanitize_text_field($_POST['instagram']) : '';
    $organizer   = isset($_POST['organizer']) ? sanitize_text_field($_POST['organizer']) : '';

    if (empty($title) || empty($description)) {
        wp_send_json_error('Пожалуйста, заполните основные поля.');
    }

    $event_id = wp_insert_post(array(
        'post_type'    => 'gh_event',
        'post_title'   => $title,
        'post_content' => $description,
        'post_status'  => 'pending',
        'post_author'  => get_current_user_id(),
    ));

    if (is_wp_error($event_id)) {
        wp_send_json_error('Ошибка при создании мероприятия.');
    }

    // Update Taxonomies
    if ($type) wp_set_object_terms($event_id, $type, 'event_type');

    // Update Meta
    update_post_meta($event_id, '_event_start_date', $start_date);
    update_post_meta($event_id, '_event_end_date', !empty($end_date) ? $end_date : $start_date);
    update_post_meta($event_id, '_event_location_country', $country);
    update_post_meta($event_id, '_event_location_city', $city);
    update_post_meta($event_id, '_event_location_text', $city . ', ' . $place);
    update_post_meta($event_id, '_event_price', $price);
    update_post_meta($event_id, '_event_reg_url', $reg_url);
    update_post_meta($event_id, '_event_whatsapp', $whatsapp);
    update_post_meta($event_id, '_event_telegram', $telegram);
    update_post_meta($event_id, '_event_instagram', $instagram);
    update_post_meta($event_id, '_event_organizer', $organizer);

    // Handle File Uploads (Photos & Docs)
    if (!empty($_FILES['event_photos']) || !empty($_FILES['event_docs'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Custom upload directory for events
        $event_dir_filter = function($dirs) {
            $dirs['subdir'] = '/events';
            $dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
            $dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
            if (!file_exists($dirs['path'])) {
                mkdir($dirs['path'], 0755, true);
            }
            return $dirs;
        };
        add_filter('upload_dir', $event_dir_filter);

        // Disable extra sizes for events
        $no_sizes_filter = function($sizes) { return array(); };
        add_filter('intermediate_image_sizes_advanced', $no_sizes_filter);

        // Handle Photos
        if (!empty($_FILES['event_photos'])) {
            // Disable generating multiple thumbnails to save disk space
            $disable_thumbnails = function($sizes) { return array(); };
            add_filter('intermediate_image_sizes_advanced', $disable_thumbnails);

            $files = $_FILES['event_photos'];
            $photo_ids = array();
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $_FILES['single_upload'] = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );

                    $attachment_id = media_handle_upload('single_upload', $event_id);

                    if (!is_wp_error($attachment_id)) {
                        $photo_ids[] = $attachment_id;
                        if (count($photo_ids) === 1 && !has_post_thumbnail($event_id)) {
                            set_post_thumbnail($event_id, $attachment_id);
                        }
                    }
                }
            }
            unset($_FILES['single_upload']);
            remove_filter('intermediate_image_sizes_advanced', $disable_thumbnails);
            update_post_meta($event_id, '_event_photos', $photo_ids);
        }

        // Handle Documents
        if (!empty($_FILES['event_docs'])) {
            $files = $_FILES['event_docs'];
            $doc_ids = array();
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $_FILES['single_upload'] = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );

                    $attachment_id = media_handle_upload('single_upload', $event_id);

                    if (!is_wp_error($attachment_id)) {
                        $doc_ids[] = $attachment_id;
                    }
                }
            }
            unset($_FILES['single_upload']);
            update_post_meta($event_id, '_event_docs', $doc_ids);
        }

        remove_filter('upload_dir', $event_dir_filter);
        remove_filter('intermediate_image_sizes_advanced', $no_sizes_filter);
    }

    wp_send_json_success('Мероприятие отправлено на модерацию.');
}
add_action('wp_ajax_gh_submit_event', 'gh_submit_event');

/**
 * Taxonomy Meta: Image for event_type
 */
function gh_event_type_add_image_field() {
    ?>
    <div class="form-field term-group">
        <label for="event_type_image">Фотография (для выбора типа)</label>
        <input type="hidden" id="event_type_image" name="event_type_image" value="">
        <div id="event_type_image_wrapper"></div>
        <p>
            <input type="button" class="button button-secondary gh_tax_media_button" id="gh_tax_media_button" value="Добавить фото" />
            <input type="button" class="button button-secondary gh_tax_media_remove" id="gh_tax_media_remove" value="Удалить" style="display:none;" />
        </p>
    </div>
    <?php
}
add_action('event_type_add_form_fields', 'gh_event_type_add_image_field', 10, 2);

function gh_event_type_edit_image_field($term) {
    $image_id = get_term_meta($term->term_id, 'event_type_image', true);
    $image_url = '';
    if ($image_id) {
        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
    }
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="event_type_image">Фотография (для выбора типа)</label></th>
        <td>
            <input type="hidden" id="event_type_image" name="event_type_image" value="<?php echo esc_attr($image_id); ?>">
            <div id="event_type_image_wrapper">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; display: block; margin-bottom: 10px;" />
                <?php endif; ?>
            </div>
            <p>
                <input type="button" class="button button-secondary gh_tax_media_button" id="gh_tax_media_button" value="Изменить фото" />
                <input type="button" class="button button-secondary gh_tax_media_remove" id="gh_tax_media_remove" value="Удалить" <?php echo empty($image_id) ? 'style="display:none;"' : ''; ?> />
            </p>
        </td>
    </tr>
    <?php
}
add_action('event_type_edit_form_fields', 'gh_event_type_edit_image_field', 10, 2);

function gh_save_event_type_image($term_id) {
    if (isset($_POST['event_type_image'])) {
        update_term_meta($term_id, 'event_type_image', sanitize_text_field($_POST['event_type_image']));
    }
}
add_action('created_event_type', 'gh_save_event_type_image', 10, 2);
add_action('edited_event_type', 'gh_save_event_type_image', 10, 2);

function gh_event_type_media_script($hook) {
    if ($hook === 'edit-tags.php' || $hook === 'term.php') {
        if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'event_type') {
            wp_enqueue_media();
            add_action('admin_footer', 'gh_event_type_media_js');
        }
    }
}
add_action('admin_enqueue_scripts', 'gh_event_type_media_script');

function gh_event_type_media_js() {
    ?>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('.gh_tax_media_button').on('click', function(e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: 'Выберите фото',
                button: { text: 'Использовать это фото' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#event_type_image').val(attachment.id);
                var url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $('#event_type_image_wrapper').html('<img src="' + url + '" style="max-width: 150px; display: block; margin-bottom: 10px;" />');
                $('.gh_tax_media_remove').show();
            });
            frame.open();
        });
        $('.gh_tax_media_remove').on('click', function(e){
            e.preventDefault();
            $('#event_type_image').val('');
            $('#event_type_image_wrapper').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Add Image Column to Taxonomy List Table
 */
function gh_event_type_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'name') {
            $new_columns['event_type_image'] = 'Фото';
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}
add_filter('manage_edit-event_type_columns', 'gh_event_type_columns');

function gh_event_type_column_content($content, $column_name, $term_id) {
    if ($column_name === 'event_type_image') {
        $image_id = get_term_meta($term_id, 'event_type_image', true);
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
            if ($image_url) {
                $content = '<img src="' . esc_url($image_url) . '" style="max-width: 50px; height: auto; border-radius: 4px;" alt="Фото типа" />';
            }
        } else {
            $content = '<span style="color: #999;">—</span>';
        }
    }
    return $content;
}
add_filter('manage_event_type_custom_column', 'gh_event_type_column_content', 10, 3);
