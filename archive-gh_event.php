<?php get_header(); ?>

    <main class="catalog-page">
        <div class="container container--wide">
            <!-- Header -->
            <div class="catalog-header">
                <h1 class="catalog-title">Все мероприятия</h1>
                <p class="catalog-subtitle">Найдите турниры, сборы и семинары по стране, по городу и дате</p>
            </div>

            <!-- Search & Sort Bar -->
            <div class="catalog-search-bar">
                <div class="search-input-wrapper">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/search-gray.svg" alt="search" class="search-icon">
                    <input type="text" placeholder="Поиск по названию, городу или организатору" class="catalog-search-input" id="eventSearch">
                </div>
                <div class="sort-dropdown">
                    <div class="custom-select">
                        <select id="eventSort">
                            <option value="near">Сначала ближайшие</option>
                            <option value="new">Сначала новые</option>
                        </select>
                        <span class="select-arrow"></span>
                    </div>
                </div>
            </div>

            <!-- Catalog Content -->
            <div class="catalog-layout">
                <!-- Sidebar Filters -->
                <aside class="catalog-sidebar">
                    <div class="filter-group">
                        <h3 class="filter-title">Фильтры</h3>
                        <div class="filter-divider"></div>
                    </div>

                    <div class="filter-group">
                        <h4 class="filter-label">Тип мероприятия</h4>
                        <div class="filter-tags" id="eventTypeFilters">
                            <button class="filter-tag active" data-type="all">Все</button>
                            <?php 
                            $types = get_terms(array('taxonomy' => 'event_type', 'hide_empty' => false));
                            $types = gh_sort_event_types($types);
                            foreach ($types as $type) : ?>
                                <button class="filter-tag" data-type="<?php echo $type->slug; ?>"><?php echo $type->name; ?></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="filter-divider"></div>
                    </div>

                    <div class="filter-group">
                        <h4 class="filter-label">Страна</h4>
                        <?php 
                        global $wpdb;
                        $existing_countries = $wpdb->get_col("
                            SELECT DISTINCT pm.meta_value 
                            FROM {$wpdb->postmeta} pm
                            JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                            WHERE pm.meta_key = '_event_location_country' 
                            AND pm.meta_value != ''
                            AND p.post_status = 'publish'
                            AND p.post_type = 'gh_event'
                            ORDER BY pm.meta_value ASC
                        ");

                        if (!empty($existing_countries)) :
                            if (count($existing_countries) > 5) : ?>
                                <div class="filter-search-wrapper">
                                    <input type="text" id="countrySearch" placeholder="Поиск страны..." class="filter-search-input">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/search-gray.svg" alt="search" class="filter-search-icon">
                                </div>
                            <?php endif; ?>
                            <div class="filter-checkboxes" id="eventLocationFilters">
                                <?php foreach ($existing_countries as $loc) : ?>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" value="<?php echo esc_attr($loc); ?>">
                                        <span class="checkmark"></span>
                                        <?php echo esc_html($loc); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="filter-checkboxes" id="eventLocationFilters">
                                <p style="color: #999; font-size: 14px;">Мероприятий пока нет</p>
                            </div>
                        <?php endif; ?>
                        <div class="filter-divider"></div>
                    </div>

                    <div class="filter-group">
                        <h4 class="filter-label">Дата проведения</h4>
                        <div class="date-range-container" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="date-picker-input" id="datePickerContainerStart" style="cursor: pointer; position: relative;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/date.svg" alt="calendar" class="input-icon">
                                <span id="dateValueStart">Дата с</span>
                                <input type="date" id="eventDateStart" class="date-input-hidden" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;">
                                <span class="select-arrow"></span>
                            </div>
                            <div class="date-picker-input" id="datePickerContainerEnd" style="cursor: pointer; position: relative;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/date.svg" alt="calendar" class="input-icon">
                                <span id="dateValueEnd">Дата по</span>
                                <input type="date" id="eventDateEnd" class="date-input-hidden" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;">
                                <span class="select-arrow"></span>
                            </div>
                        </div>
                    </div>

                    <div class="filter-group filter-actions">
                        <button class="btn btn--black btn--apply" id="applyFilters">Применить</button>
                    </div>
                </aside>

                <!-- Events Grid -->
                <div class="catalog-grid">
                    <div class="events-list" id="eventsGrid">
                        <?php
                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                        $args = array(
                            'post_type'      => 'gh_event',
                            'posts_per_page' => 12,
                            'paged'          => $paged,
                            'post_status'    => 'publish',
                            'meta_key'       => '_event_start_date',
                            'orderby'        => 'meta_value',
                            'order'          => 'ASC'
                        );
                        $meta_query = array('relation' => 'AND');
                        $selected_date_start = !empty($_GET['date_start']) ? sanitize_text_field($_GET['date_start']) : '';
                        $selected_date_end = !empty($_GET['date_end']) ? sanitize_text_field($_GET['date_end']) : '';
                        $selected_date = !empty($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
                        $selected_country = !empty($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
                        
                        if (!empty($selected_date_start) || !empty($selected_date_end)) {
                            if (!empty($selected_date_start) && !empty($selected_date_end)) {
                                $meta_query[] = array(
                                    'relation' => 'AND',
                                    array(
                                        'key'     => '_event_start_date',
                                        'value'   => $selected_date_end,
                                        'compare' => '<=',
                                        'type'    => 'DATE'
                                    ),
                                    array(
                                        'relation' => 'OR',
                                        array(
                                            'key'     => '_event_end_date',
                                            'value'   => $selected_date_start,
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
                                                'value'   => $selected_date_start,
                                                'compare' => '>=',
                                                'type'    => 'DATE'
                                            )
                                        )
                                    )
                                );
                            } elseif (!empty($selected_date_start)) {
                                $meta_query[] = array(
                                    'relation' => 'OR',
                                    array(
                                        'key'     => '_event_start_date',
                                        'value'   => $selected_date_start,
                                        'compare' => '>=',
                                        'type'    => 'DATE'
                                    ),
                                    array(
                                        'key'     => '_event_end_date',
                                        'value'   => $selected_date_start,
                                        'compare' => '>=',
                                        'type'    => 'DATE'
                                    )
                                );
                            } else {
                                $meta_query[] = array(
                                    'key'     => '_event_start_date',
                                    'value'   => $selected_date_end,
                                    'compare' => '<=',
                                    'type'    => 'DATE'
                                );
                            }
                        } elseif (!empty($selected_date)) {
                            $meta_query[] = array(
                                'relation' => 'AND',
                                array(
                                    'key'     => '_event_start_date',
                                    'value'   => $selected_date,
                                    'compare' => '<=',
                                    'type'    => 'DATE'
                                ),
                                array(
                                    'relation' => 'OR',
                                    array(
                                        'key'     => '_event_end_date',
                                        'value'   => $selected_date,
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
                                            'value'   => $selected_date,
                                            'compare' => '=',
                                            'type'    => 'DATE'
                                        )
                                    )
                                )
                            );
                        } else {
                            // Default: Show events that haven't ended yet
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
 
                        $selected_type = !empty($_GET['e_type']) ? $_GET['e_type'] : (!empty($_GET['event_type']) ? $_GET['event_type'] : 'all');
                        if ($selected_type !== 'all') {
                            $args['tax_query'] = array(
                                array(
                                    'taxonomy' => 'event_type',
                                    'field'    => 'slug',
                                    'terms'    => sanitize_text_field($selected_type),
                                )
                            );
                        }
 
                        if (!empty($selected_country)) {
                            $meta_query[] = array(
                                'key'     => '_event_location_country',
                                'value'   => $selected_country,
                                'compare' => 'LIKE'
                            );
                        }
 
                        if (!empty($_GET['city'])) {
                            $meta_query[] = array(
                                'key'     => '_event_location_city',
                                'value'   => sanitize_text_field($_GET['city']),
                                'compare' => 'LIKE'
                            );
                        }
 
                        $args['meta_query'] = $meta_query;
                        $query = new WP_Query($args);
 
                        if ($query->have_posts()) {
                            $active_ads = gh_get_active_ads('3', -1);
                            $ad_index = 0;
                            $counter = 0;
                            $sorted_posts = gh_sort_events_by_type($query->posts);
                            global $post;
                            foreach ($sorted_posts as $post) {
                                setup_postdata($post);
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
                                    $ad_img  = $ad_card ? $ad_card['image'] : get_template_directory_uri() . '/img/ad-test.png';
                                    $ad_link = $ad_card ? $ad_card['link'] : '#!';
                                    $is_empty_ad = empty($ad_card);
                                    ?>
                                    <div class="event-ad-card">
                                        <a href="<?php echo esc_url($ad_link); ?>" <?php echo ($ad_link !== '#!') ? 'target="_blank"' : ''; ?> style="display:block;height:100%;min-height:400px;text-decoration:none;">
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
                            }
                            wp_reset_postdata();

                            // Append remaining unused ads at the end
                            if (!empty($active_ads) && $ad_index < count($active_ads)) {
                                for ($i = $ad_index; $i < count($active_ads); $i++) {
                                    $ad_card = $active_ads[$i];
                                    ?>
                                    <div class="event-ad-card">
                                        <a href="<?php echo esc_url($ad_card['link']); ?>" <?php echo ($ad_card['link'] !== '#!') ? 'target="_blank"' : ''; ?> style="display:block;height:100%;min-height:400px;text-decoration:none;">
                                            <div class="event-ad-card__bg">
                                                <img src="<?php echo esc_url($ad_card['image']); ?>" alt="Advertisement">
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                }
                            }
                        } else { ?>
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
                        <?php } ?>
                    </div><!-- /events-list #eventsGrid -->
                </div><!-- /catalog-grid -->
            </div><!-- /catalog-layout -->
        </div><!-- /container--wide -->
    </main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('eventsGrid');
    const searchInput = document.getElementById('eventSearch');
    const sortSelect = document.getElementById('eventSort');
    const typeBtns = document.querySelectorAll('#eventTypeFilters .filter-tag');
    const locationChecks = document.querySelectorAll('#eventLocationFilters input');
    const dateInputStart = document.getElementById('eventDateStart');
    const dateInputEnd = document.getElementById('eventDateEnd');
    const applyBtn = document.getElementById('applyFilters');

    const urlParams = new URLSearchParams(window.location.search);
    let currentType = urlParams.get('e_type') || urlParams.get('event_type') || 'all';
    const initialCountry = urlParams.get('country');
    const initialDateStart = urlParams.get('date_start') || urlParams.get('date');
    const initialDateEnd = urlParams.get('date_end');

    // Set active tab based on URL
    typeBtns.forEach(btn => {
        if (btn.dataset.type === currentType) {
            typeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    });

    // Sync country checkboxes
    if (initialCountry) {
        locationChecks.forEach(check => {
            if (check.value === initialCountry) check.checked = true;
        });
    }

    // Sync date pickers
    if (initialDateStart && dateInputStart) {
        dateInputStart.value = initialDateStart;
        document.getElementById('dateValueStart').textContent = initialDateStart;
    }
    if (initialDateEnd && dateInputEnd) {
        dateInputEnd.value = initialDateEnd;
        document.getElementById('dateValueEnd').textContent = initialDateEnd;
    }

    function fetchEvents(page = 1) {
        grid.style.opacity = '0.5';
        
        const urlParams = new URLSearchParams(window.location.search);
        const searchVal = searchInput.value || urlParams.get('ad_search') || '';
        const countryVal = urlParams.get('country') || '';
        const cityVal = urlParams.get('city') || '';

        const locations = Array.from(locationChecks)
            .filter(i => i.checked)
            .map(i => i.value);

        const formData = new FormData();
        formData.append('action', 'gh_filter_events');
        formData.append('search', searchVal);
        formData.append('sort', sortSelect.value);
        formData.append('type', currentType);
        formData.append('locations', JSON.stringify(locations));
        formData.append('date_start', dateInputStart ? dateInputStart.value : '');
        formData.append('date_end', dateInputEnd ? dateInputEnd.value : '');
        formData.append('country', countryVal);
        formData.append('city', cityVal);

        fetch(themeData.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                grid.innerHTML = data.data;
            }
            grid.style.opacity = '1';
        });
    }

    // Initial fetch if we have params
    if (window.location.search) fetchEvents();

    typeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            typeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentType = btn.dataset.type;
            fetchEvents();
        });
    });

    applyBtn.addEventListener('click', fetchEvents);
    sortSelect.addEventListener('change', fetchEvents);

    // Debounce search
    let timeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(fetchEvents, 500);
    });

    // Country search filter (client-side)
    const countrySearch = document.getElementById('countrySearch');
    if (countrySearch) {
        countrySearch.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            locationChecks.forEach(check => {
                const label = check.closest('.custom-checkbox');
                if (label) {
                    const text = label.textContent.trim().toLowerCase();
                    if (text.includes(val)) {
                        label.style.display = 'flex';
                    } else {
                        label.style.display = 'none';
                    }
                }
            });
        });
    }

    // Date picker display update
    if (dateInputStart) {
        dateInputStart.addEventListener('change', () => {
            document.getElementById('dateValueStart').textContent = dateInputStart.value ? dateInputStart.value : 'Дата с';
        });
    }
    if (dateInputEnd) {
        dateInputEnd.addEventListener('change', () => {
            document.getElementById('dateValueEnd').textContent = dateInputEnd.value ? dateInputEnd.value : 'Дата по';
        });
    }

    // Explicitly trigger date picker on container click
    const dateContainerStart = document.getElementById('datePickerContainerStart');
    if (dateContainerStart && dateInputStart) {
        dateContainerStart.addEventListener('click', (e) => {
            if (e.target !== dateInputStart) {
                if (typeof dateInputStart.showPicker === 'function') {
                    dateInputStart.showPicker();
                } else {
                    dateInputStart.click();
                }
            }
        });
    }

    const dateContainerEnd = document.getElementById('datePickerContainerEnd');
    if (dateContainerEnd && dateInputEnd) {
        dateContainerEnd.addEventListener('click', (e) => {
            if (e.target !== dateInputEnd) {
                if (typeof dateInputEnd.showPicker === 'function') {
                    dateInputEnd.showPicker();
                } else {
                    dateInputEnd.click();
                }
            }
        });
    }
});
</script>

<?php get_footer(); ?>
