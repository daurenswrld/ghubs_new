<?php
/**
 * Handle Gallery AJAX Filtering
 */
add_action('wp_ajax_filter_gallery', 'gh_ajax_filter_gallery');
add_action('wp_ajax_nopriv_filter_gallery', 'gh_ajax_filter_gallery');

function gh_ajax_filter_gallery() {
    $search    = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    $type      = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'all';
    $countries = isset($_POST['countries']) ? (array)$_POST['countries'] : array();
    $date      = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
    $sort      = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'newest';
    $paged     = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = array(
        'post_type'      => 'gallery_album',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $paged,
    );

    // Search logic
    if ($search) {
        $args['s'] = $search;
    }

    // Sorting
    if ($sort === 'newest') {
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    } elseif ($sort === 'oldest') {
        $args['orderby'] = 'date';
        $args['order']   = 'ASC';
    }

    $meta_query = array('relation' => 'AND');

    // Type Filter
    if ($type !== 'all') {
        $meta_query[] = array(
            'key'     => '_gh_category',
            'value'   => $type,
            'compare' => '=',
        );
    }

    // Country Filter
    if (!empty($countries)) {
        $meta_query[] = array(
            'key'     => '_gh_country',
            'value'   => $countries,
            'compare' => 'IN',
        );
    }

    // Date Filter
    if ($date) {
        $meta_query[] = array(
            'key'     => '_gh_dates',
            'value'   => $date,
            'compare' => '=',
        );
    }

    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $album_id = get_the_ID();
            $country  = get_post_meta($album_id, '_gh_country', true);
            $city     = get_post_meta($album_id, '_gh_city', true);
            $dates    = get_post_meta($album_id, '_gh_dates', true);
            $category = get_post_meta($album_id, '_gh_category', true);
            $photos   = get_post_meta($album_id, '_gh_album_photos', true);
            
            $cat_label = 'Турнир';
            if ($category === 'camps') $cat_label = 'Сборы';
            if ($category === 'seminars') $cat_label = 'Семинары и мастер классы';

            $location = array_filter([$country, $city]);
            $location_str = !empty($location) ? implode(', ', $location) : 'Локация не указана';
            
            $bg_image = get_template_directory_uri() . '/img/choice-camp.jpg'; // placeholder
            if (has_post_thumbnail()) {
                $bg_image = get_the_post_thumbnail_url($album_id, 'medium_large');
            } elseif (!empty($photos) && is_array($photos)) {
                $bg_image = wp_get_attachment_image_url($photos[0], 'medium_large');
            }
            ?>
            <a href="<?php the_permalink(); ?>" class="gallery-archive-card">
                <div class="gallery-archive-card__media stacked-media">
                    <img src="<?php echo esc_url($bg_image); ?>" style="background:#e0e0e0; object-fit: cover; width: 100%; height: 100%;" alt="<?php echo esc_attr(get_the_title()); ?>">
                </div>
                <div class="gallery-archive-card__content">
                    <h3 class="gallery-archive-card__title"><?php the_title(); ?></h3>
                    <div class="gallery-archive-card__tags">
                        <div class="event-tag event-tag--ghost">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                            <?php echo esc_html($cat_label); ?>
                        </div>
                    </div>
                    <div class="gallery-archive-card__meta">
                        <div class="meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <?php echo esc_html($location_str); ?>
                        </div>
                    </div>
                    <div class="gallery-archive-card__bottom">
                        <div class="meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <?php echo $dates ? esc_html(date('d.m.Y', strtotime($dates))) : 'Даты не указаны'; ?>
                        </div>
                        <div class="gallery-archive-card__arrow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
                </div>
            </a>
            <?php
        }
        
        // Pagination
        $total_pages = $query->max_num_pages;
        if ($total_pages > 1) {
            echo '<div class="pagination" style="grid-column: 1/-1; display:flex; justify-content:center; gap:10px; margin-top:20px;">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i === $paged) ? 'current' : '';
                echo '<a href="#" class="page-numbers ' . $active . '" data-page="' . $i . '">' . $i . '</a>';
            }
            echo '</div>';
        }
    } else {
        echo '
        <div class="empty-state" style="grid-column: 1/-1; padding: 60px 20px; text-align: center; background: #fff; border-radius: 32px; border: 1px dashed #e0e0e0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px;">
            <div class="empty-state__icon" style="width: 60px; height: 60px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="empty-state__content">
                <h3 style="font-size: 20px; color: #1a1a1a; font-weight: 600; margin-bottom: 5px;">Ничего не найдено</h3>
                <p style="color: #888; font-size: 15px; margin: 0; line-height: 1.4;">Попробуйте изменить параметры фильтрации или поисковый запрос.</p>
            </div>
        </div>';
    }

    $html = ob_get_clean();
    wp_send_json_success($html);
}
