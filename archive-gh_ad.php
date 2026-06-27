<?php
/**
 * Template Name: Объявления
 */
get_header(); ?>

    <main class="main-content ads-page">
        <div class="container container--wide">
            <div class="ads-header-row">
                <div class="ads-header-text">
                    <h1 class="page-title">Все объявления</h1>
                    <p class="page-subtitle">Смотрите актуальные объявления</p>
                </div>
                <div class="ads-header-actions">
                    <a href="<?php echo esc_url(home_url('/add-ad/')); ?>" class="btn btn--black btn--pill">
                        <span class="plus-icon">+</span> Добавить объявление
                    </a>
                </div>
            </div>

            <div class="ads-filters">
                <form class="ads-search-box" method="get">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/search-gray.svg" alt="search" class="search-icon">
                    <input type="text" name="ad_search" placeholder="Поиск по названию" value="<?php echo isset($_GET['ad_search']) ? esc_attr($_GET['ad_search']) : ''; ?>">
                </form>
                <div class="ads-sort-box">
                    <select id="adSort">
                        <option value="new">Сначала новые</option>
                        <option value="old">Сначала старые</option>
                    </select>
                </div>
            </div>

            <div class="ads-grid-container">
                <div class="ads-grid">
                    <?php
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $args = array(
                        'post_type'      => 'gh_ad',
                        'posts_per_page' => 12,
                        'paged'          => $paged,
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => (isset($_GET['sort']) && $_GET['sort'] == 'old') ? 'ASC' : 'DESC'
                    );

                    if (isset($_GET['ad_search']) && !empty($_GET['ad_search'])) {
                        $args['s'] = sanitize_text_field($_GET['ad_search']);
                    }

                    $query = new WP_Query($args);

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
                                <div class="ad-card__content">
                                    <h3 class="ad-card__title"><?php echo mb_strimwidth(get_the_title(), 0, 45, '...'); ?></h3>
                                    <p class="ad-card__text"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
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
                        <?php endwhile; ?>
                        
                        <div class="pagination-container" style="grid-column: 1/-1; margin-top: 40px; display: flex; justify-content: center;">
                            <?php
                            echo paginate_links(array(
                                'total' => $query->max_num_pages,
                                'current' => $paged,
                                'prev_text' => '&larr;',
                                'next_text' => '&rarr;',
                                'base' => '#%#%',
                                'format' => '?paged=%#%',
                            ));
                            ?>
                        </div>

                        <?php wp_reset_postdata();
                    else : ?>
                        <div class="empty-state" style="grid-column: 1/-1; padding: 80px 20px; text-align: center; background: #fff; border-radius: 32px; border: 1px dashed #e0e0e0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px;">
                            <div class="empty-state__icon" style="width: 80px; height: 80px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                            </div>
                            <div class="empty-state__content">
                                <h3 style="font-size: 24px; color: #1a1a1a; font-weight: 600; margin-bottom: 8px;">Объявлений пока нет</h3>
                                <p style="color: #666; font-size: 16px; max-width: 400px; margin: 0 auto 24px; line-height: 1.5;">Будьте первым, кто предложит свои товары или услуги нашему спортивному сообществу!</p>
                                <a href="<?php echo esc_url(home_url('/add-ad/')); ?>" class="btn btn--black" style="display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 100px; font-weight: 600; transition: all 0.3s ease;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    Разместить первое объявление
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const adsGrid = document.querySelector('.ads-grid');
    const searchInput = document.querySelector('.ads-search-box input');
    const sortSelect = document.getElementById('adSort');
    let searchTimeout;

    function fetchAds(page = 1) {
        adsGrid.style.opacity = '0.5';
        
        const formData = new FormData();
        formData.append('action', 'gh_search_ads');
        formData.append('search', searchInput.value);
        formData.append('sort', sortSelect.value);
        formData.append('paged', page);

        fetch(themeData.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                adsGrid.innerHTML = data.data;
                adsGrid.style.opacity = '1';
                
                // Update URL without reload
                const url = new URL(window.location.href);
                if (searchInput.value) url.searchParams.set('ad_search', searchInput.value);
                else url.searchParams.delete('ad_search');
                url.searchParams.set('sort', sortSelect.value);
                if (page > 1) url.searchParams.set('paged', page);
                else url.searchParams.delete('paged');
                window.history.pushState({}, '', url);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Search with Debounce
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchAds(1);
        }, 500);
    });

    // Sort Change
    sortSelect.addEventListener('change', () => {
        fetchAds(1);
    });

    // Pagination Click Handling (Delegation)
    document.addEventListener('click', (e) => {
        const pageLink = e.target.closest('.pagination-container a, .pagination a');
        if (pageLink) {
            e.preventDefault();
            const url = new URL(pageLink.href);
            const page = url.searchParams.get('paged') || 1;
            fetchAds(page);
            window.scrollTo({ top: adsGrid.offsetTop - 100, behavior: 'smooth' });
        }
    });

    // Prevent form submission
    document.querySelector('.ads-search-box').addEventListener('submit', (e) => e.preventDefault());
});
</script>
