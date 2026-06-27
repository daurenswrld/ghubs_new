<?php 
/* Template Name: Галерея */ 
get_header(); 
?>

    <main class="main-content gallery-page">
        <section class="gallery-section">
            <div class="container container--wide">
                <div class="gallery-header">
                    <div class="gallery-header__left">
                        <h1 class="page-title">Галерея</h1>
                        <p class="page-subtitle">Медиаархив спортивных мероприятий: фото, видео и яркие моменты гимнастического сообщества</p>
                    </div>
                    <div class="gallery-header__right">
                        <a href="<?php echo esc_url(home_url('/add-album/')); ?>" class="btn btn--black btn--pill">
                            <span class="plus-icon">+</span> Опубликовать альбом
                        </a>
                    </div>
                </div>

                <div class="gallery-search-row">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="gallerySearch" placeholder="Поиск по названию, городу или организатору">
                    </div>
                    <div class="sort-select-wrapper">
                        <select id="gallerySort">
                            <option value="newest">Сначала новые</option>
                            <option value="oldest">Сначала старые</option>
                        </select>
                        <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>

                <div class="gallery-layout">
                    <!-- Sidebar -->
                    <aside class="sidebar-filters">
                        <h3 class="sidebar-title">Фильтры</h3>
                        
                        <div class="filter-group">
                            <h4 class="filter-label">Тип мероприятия</h4>
                            <div class="filter-pills" id="galleryTypeFilter">
                                <label class="filter-pill active"><input type="radio" name="type" value="all" checked><span>Все</span></label>
                                <label class="filter-pill"><input type="radio" name="type" value="tournaments"><span>Турниры</span></label>
                                <label class="filter-pill"><input type="radio" name="type" value="camps"><span>Сборы</span></label>
                                <label class="filter-pill"><input type="radio" name="type" value="seminars"><span>Семинары и мастер классы</span></label>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h4 class="filter-label">Страна</h4>
                            <div class="filter-checkboxes" id="galleryCountryFilter">
                                <?php
                                // Direct query to ensure list is always fresh (cache removed as requested)
                                global $wpdb;
                                $active_countries = $wpdb->get_col("
                                    SELECT DISTINCT pm.meta_value 
                                    FROM {$wpdb->postmeta} pm
                                    INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                                    WHERE pm.meta_key = '_gh_country' 
                                      AND pm.meta_value != ''
                                      AND pm.meta_value != '0'
                                      AND p.post_status = 'publish'
                                      AND p.post_type = 'gallery_album'
                                ");
                                
                                if (!empty($active_countries)) :
                                    $active_countries = array_unique(array_filter((array)$active_countries));
                                    asort($active_countries);
                                    
                                    foreach ($active_countries as $country) : 
                                ?>
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="country[]" value="<?php echo esc_attr($country); ?>">
                                            <span class="label-text"><?php echo esc_html($country); ?></span>
                                        </label>
                                <?php 
                                    endforeach;
                                endif; ?>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h4 class="filter-label">Дата проведения</h4>
                            <div class="date-input-wrapper">
                                <svg class="calendar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <input type="date" id="galleryDateFilter" placeholder="Выберите дату">
                                <svg class="arrow-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>

                        <button class="btn btn--black btn--full">Применить</button>
                    </aside>

                    <!-- Main Grid -->
                    <div class="gallery-cards-grid" id="galleryResults">
                        <?php
                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                        $args = array(
                            'post_type'      => 'gallery_album',
                            'post_status'    => 'publish',
                            'posts_per_page' => 12,
                            'paged'          => $paged
                        );
                        $album_query = new WP_Query($args);

                        if ($album_query->have_posts()) {
                            while ($album_query->have_posts()) {
                                $album_query->the_post();
                                $country  = get_post_meta(get_the_ID(), '_gh_country', true);
                                $city     = get_post_meta(get_the_ID(), '_gh_city', true);
                                $dates    = get_post_meta(get_the_ID(), '_gh_dates', true);
                                $category = get_post_meta(get_the_ID(), '_gh_category', true);
                                $photos   = get_post_meta(get_the_ID(), '_gh_album_photos', true);
                                
                                $cat_label = 'Турнир';
                                if ($category === 'camps') $cat_label = 'Сборы';
                                if ($category === 'seminars') $cat_label = 'Семинары и мастер классы';

                                $location = array_filter([$country, $city]);
                                $location_str = !empty($location) ? implode(', ', $location) : 'Локация не указана';
                                
                                $bg_image = get_template_directory_uri() . '/img/choice-camp.jpg'; // placeholder
                                if (has_post_thumbnail()) {
                                    $bg_image = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
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
                            } // end while
                            
                            // Pagination
                            $total_pages = $album_query->max_num_pages;
                            if ($total_pages > 1) {
                                echo '<div class="pagination" style="grid-column: 1/-1; display:flex; justify-content:center; gap:10px; margin-top:20px;">';
                                echo paginate_links(array(
                                    'base'      => get_pagenum_link(1) . '%_%',
                                    'format'    => 'page/%#%',
                                    'current'   => $paged,
                                    'total'     => $total_pages,
                                    'prev_text' => '&laquo; Назад',
                                    'next_text' => 'Вперед &raquo;',
                                ));
                                echo '</div>';
                            }
                            
                        } else {
                            echo '
                            <div class="empty-state" style="grid-column: 1/-1; padding: 80px 20px; text-align: center; background: #fff; border-radius: 32px; border: 1px dashed #e0e0e0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px;">
                                <div class="empty-state__icon" style="width: 80px; height: 80px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                                <div class="empty-state__content">
                                    <h3 style="font-size: 24px; color: #1a1a1a; font-weight: 600; margin-bottom: 8px;">Здесь пока пусто</h3>
                                    <p style="color: #666; font-size: 16px; max-width: 400px; margin: 0 auto 24px; line-height: 1.5;">Станьте первым, кто добавит яркие моменты гимнастики в наш медиаархив!</p>
                                    <a href="' . esc_url(home_url('/add-album/')) . '" class="btn btn--black" style="display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        Добавить первый альбом
                                    </a>
                                </div>
                            </div>';
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const resultsContainer = document.getElementById('galleryResults');
    const searchInput      = document.getElementById('gallerySearch');
    const sortSelect       = document.getElementById('gallerySort');
    const dateFilter       = document.getElementById('galleryDateFilter');
    const typePills        = document.querySelectorAll('#galleryTypeFilter input');
    const countryChecks    = document.querySelectorAll('#galleryCountryFilter input');
    const applyBtn         = document.querySelector('.btn--full'); // sidebar apply button

    let currentPage = 1;

    async function updateGallery(page = 1) {
        currentPage = page;
        
        // Visual feedback
        resultsContainer.style.opacity = '0.5';
        resultsContainer.style.pointerEvents = 'none';

        const formData = new FormData();
        formData.append('action', 'filter_gallery');
        formData.append('paged', page);
        formData.append('s', searchInput.value);
        formData.append('sort', sortSelect.value);
        formData.append('date', dateFilter.value);
        
        // Type
        const activeType = document.querySelector('#galleryTypeFilter input:checked');
        if (activeType) {
            formData.append('type', activeType.value);
        }

        // Countries
        const checkedCountries = document.querySelectorAll('#galleryCountryFilter input:checked');
        checkedCountries.forEach(cb => {
            formData.append('countries[]', cb.value);
        });

        try {
            const response = await fetch(themeData.ajax_url, {
                method: 'POST',
                body: formData
            });
            const res = await response.json();
            
            if (res.success) {
                resultsContainer.innerHTML = res.data;
                // Scroll to top of results if it's a page change
                if (page > 1 || searchInput.value || checkedCountries.length > 0) {
                    resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        } catch (err) {
            console.error('Gallery Filter Error:', err);
        } finally {
            resultsContainer.style.opacity = '1';
            resultsContainer.style.pointerEvents = 'all';
        }
    }

    // Event Listeners
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => updateGallery(1), 500);
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', () => updateGallery(1));
    }

    if (dateFilter) {
        dateFilter.addEventListener('change', () => updateGallery(1));
    }

    typePills.forEach(pill => {
        pill.addEventListener('change', (e) => {
            // Update active class on pills
            document.querySelectorAll('.filter-pill').forEach(l => l.classList.remove('active'));
            e.target.closest('.filter-pill').classList.add('active');
            updateGallery(1);
        });
    });

    if (applyBtn) {
        applyBtn.addEventListener('click', (e) => {
            e.preventDefault();
            updateGallery(1);
        });
    }

    // Handle pagination clicks (delegated)
    resultsContainer.addEventListener('click', (e) => {
        const pageLink = e.target.closest('.page-numbers');
        if (pageLink && !pageLink.classList.contains('current')) {
            e.preventDefault();
            const page = pageLink.getAttribute('data-page');
            if (page) {
                updateGallery(parseInt(page));
            }
        }
    });
});
</script>
