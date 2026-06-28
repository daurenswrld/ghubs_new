<?php
/*
 Template Name: Главная страница
*/
get_header(); ?>


<main>
        <section class="hero-section">
            <div class="container hero-section__grid">
                <div class="hero-content">
                    <h1 class="hero-content__title">
                        <?php 
                        $default_title = "Находи и публикуй <br> спортивные мероприятия <br> по всему миру";
                        echo wp_kses_post(gh_get_option('gh_hero_title', $default_title)); 
                        ?>
                    </h1>

                    <form class="search-card" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <input type="hidden" name="post_type" value="gh_event">
                        <div class="search-card__tabs">
                            <button type="button" class="tab-btn active" data-type="all">Все</button>
                            <?php 
                            $types = get_terms(array('taxonomy' => 'event_type', 'hide_empty' => false));
                            $types = gh_sort_event_types($types);
                            foreach ($types as $type) : ?>
                                <button type="button" class="tab-btn" data-type="<?php echo $type->slug; ?>"><?php echo $type->name; ?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="e_type" id="heroEventType" value="all">
                        <div class="search-card__body">
                            <div class="input-group">
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
                                ?>
                                <select name="country" class="search-input" style="appearance: none; background-image: url('<?php echo get_template_directory_uri(); ?>/img/arrow-down.svg'); background-repeat: no-repeat; background-position: right 20px center; background-size: 12px;">
                                    <option value="">Все страны</option>
                                    <?php foreach ($existing_countries as $loc) : ?>
                                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="date-row" style="margin-top: 10px; display:flex; flex-direction:column; gap:10px; align-items:flex-start;">
                                <span class="date-label">Дата проведения</span>
                                <div class="date-picker-bar" style="width:100%">
                                    <input type="date" name="date_start" class="search-input" style="background:none;padding:0;width: 50%;border-radius:0;border:0;outline:none;" placeholder="с">
                                    <div class="date-separator"></div>
                                    <input type="date" name="date_end" class="search-input" style="background:none;padding:0;width: 50%;border-radius:0;border:0;outline:none;" placeholder="по">
                                </div>
                            </div>
                            <button type="submit" class="btn btn--black btn--find" style="margin-top: 10px; width: 100%;">Найти</button>
                        </div>
                    </form>
                </div>

                <div class="hero-bottom-actions">
                    <a href="<?php echo home_url('/ads/'); ?>" class="btn btn--outline-white">Объявления</a>
                    <a href="<?php echo get_post_type_archive_link('gh_event'); ?>" class="btn btn--white">Все мероприятия</a>
                </div>
            </div>
            
            <div class="hero-background">
                <!-- User will add image here -->
                <div class="spotlight"></div>
            </div>
        </section>

        <section class="about">
            <div class="container container--wide">
                <div class="about__grid">
                    <div class="about__label">О нас</div>
                    <div class="about__content">
                        <p class="about__text">
                            <?php 
                            $default_about = '<strong>GymnasticsHub</strong> — международная точка доступа к миру художественной гимнастики. Мы создали платформу, где время работает на вас. Наша главная задача — обеспечить максимально простой и быстрый поиск, а также мгновенную публикацию мероприятий по художественной гимнастике по всему миру. Больше никаких долгих регистраций и сложных форм: находите турниры, учебно-тренировочные сборы, мастер-классы, семинары или представляйте свои проекты глобальному гимнастическому сообществу в один клик. Все самое важное теперь собрано в едином, удобном и интуитивном интерфейсе.';
                            echo wp_kses_post(gh_get_option('gh_about_text', $default_about)); 
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="ads">
            <div class="container container--wide">
                <div class="ads__grid">
                    <?php 
                    $ads1 = gh_get_active_ads('1', 2); 
                    if (!empty($ads1)) :
                        foreach ($ads1 as $ad) : ?>
                            <a href="<?php echo esc_url($ad['link']); ?>" class="ads__item" <?php echo ($ad['link'] !== '#!') ? 'target="_blank"' : ''; ?>>
                                <img src="<?php echo esc_url($ad['image']); ?>" alt="Advertising" class="ads__img" style="display:block; width:100%; height:100%; object-fit:cover;">
                            </a>
                        <?php endforeach;
                    else: ?>
                        <a href="#!" class="ads__item"><span class="ads__placeholder">Реклама</span></a>
                        <a href="#!" class="ads__item"><span class="ads__placeholder">Реклама</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="promo-slider">
            <div class="promo-slider__track">
                <?php 
                $ads2 = gh_get_active_ads('2', -1);
                if (!empty($ads2)) :
                    $first = true;
                    foreach ($ads2 as $ad) : ?>
                        <a href="<?php echo esc_url($ad['link']); ?>" class="promo-slide <?php echo $first ? 'active' : ''; ?>" <?php echo ($ad['link'] !== '#!') ? 'target="_blank"' : ''; ?>>
                            <img src="<?php echo esc_url($ad['image']); ?>" alt="Gymnastics Event">
                        </a>
                    <?php $first = false; endforeach;
                else: ?>
                    <a href="#!" class="promo-slide active">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/test-banner.webp" alt="Gymnastics Event">
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <section class="events">
            <div class="container container--wide">
                <div class="section-header">
                    <h2 class="section-title">Мероприятия</h2>
                    <p class="section-subtitle">Откройте для себя мир гимнастики: от международных турниров до профессиональных мастер-классов</p>
                </div>
                
                <div class="events__grid">
                    <?php
                    $event_args = array(
                        'post_type'      => 'gh_event',
                        'posts_per_page' => 3,
                        'post_status'    => 'publish',
                        'meta_key'       => '_event_start_date',
                        'orderby'        => 'meta_value',
                        'order'          => 'ASC',
                        'meta_query'     => array(
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
                        )
                    );
                    $event_query = new WP_Query($event_args);
                    $counter = 0;

                    if ($event_query->have_posts()) :
                        $sorted_posts = gh_sort_events_by_type($event_query->posts);
                        global $post;
                        foreach ($sorted_posts as $post) : setup_postdata($post);
                            $counter++;
                            $date = get_post_meta(get_the_ID(), '_event_start_date', true);
                            $end = get_post_meta(get_the_ID(), '_event_end_date', true);
                            $country = get_post_meta(get_the_ID(), '_event_location_country', true);
                            $city = get_post_meta(get_the_ID(), '_event_location_city', true);
                            
                            $location_display = '';
                            if ($country && $city) {
                                $location_display = $country . ', ' . $city;
                            } elseif ($country) {
                                $location_display = $country;
                            } elseif ($city) {
                                $location_display = $city;
                            } else {
                                $location_display = 'Локация не указана';
                            }

                            $terms = get_the_terms(get_the_ID(), 'event_type');
                            $type_name = !empty($terms) && !is_wp_error($terms) ? $terms[0]->name : 'Мероприятие';
                            
                            // Insert Ad Banner after 2nd card
                            if ($counter == 3) : ?>
                                <!-- Vertical Ad Banner -->
                                <?php 
                                $ads3 = gh_get_active_ads('3', 1);
                                $ad3_img = !empty($ads3) ? $ads3[0]['image'] : get_template_directory_uri() . '/img/ad-test.png';
                                $ad3_link = !empty($ads3) ? $ads3[0]['link'] : '#!';
                                ?>
                                <div class="event-ad-card">
                                    <a href="<?php echo esc_url($ad3_link); ?>" <?php echo ($ad3_link !== '#!') ? 'target="_blank"' : ''; ?>>
                                        <div class="event-ad-card__bg">
                                            <img src="<?php echo esc_url($ad3_img); ?>" alt="Advertisement">
                                        </div>
                                        <?php if (empty($ads3)) : ?>
                                        <div class="event-ad-card__content">
                                            <h3 class="ad-title">JOIN OUR <br>COMMUNITY</h3>
                                            <p class="ad-subtitle">Everything about gymnastics</p>
                                        </div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="event-card">
                                <div class="event-card__image">
                                    <?php if (has_post_thumbnail()) : the_post_thumbnail('large'); else : ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/card.png" alt="Card">
                                    <?php endif; ?>
                                </div>
                                <div class="event-card__content">
                                    <div class="event-card__top">
                                        <div class="event-card__meta">
                                            <span class="meta-tag"><?php echo esc_html($type_name); ?></span>
                                        </div>
                                        <h3 class="event-card__title"><?php the_title(); ?></h3>
                                        <div class="meta-item location">
                                            <img src="<?php echo get_template_directory_uri(); ?>/img/geo.svg" alt="Location" class="meta-icon">
                                            <span><?php echo esc_html($location_display); ?></span>
                                        </div>
                                    </div>
                                    <div class="event-card__bottom">
                                        <div class="meta-item date">
                                            <img src="<?php echo get_template_directory_uri(); ?>/img/date-gold.svg" alt="Date" class="meta-icon">
                                            <span><?php echo gh_format_event_date($date, $end); ?></span>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="event-card__arrow">
                                            <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right.svg" alt="Details">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; wp_reset_postdata();
                    else : ?>
                        <div class="empty-state-home" style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; background: rgba(0,0,0,0.03); border: 1px dashed rgba(0,0,0,0.1); border-radius: 24px; width: 100%;">
                            <div class="empty-state-home__icon" style="margin-bottom: 16px; opacity: 0.5;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #1a1a1a;">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <h3 style="font-size: 20px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px;">Мероприятий пока нет</h3>
                            <p style="color: rgba(0,0,0,0.5); font-size: 15px; max-width: 320px; margin: 0 auto;">Следите за обновлениями, скоро здесь появятся новые события!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="events__footer">
                    <a href="<?php echo get_post_type_archive_link('gh_event'); ?>" class="btn btn--outline-dark btn--show-more">Показать больше</a>
                </div>
            </div>
        </section>

       <section class="banners-ads">
            <div class="container container--wide">
                <div class="banners-ads__grid">
                    <?php 
                    $ads4 = gh_get_active_ads('4', 4);
                    if (!empty($ads4)) :
                        foreach ($ads4 as $ad) : ?>
                            <a href="<?php echo esc_url($ad['link']); ?>" class="banners-ads__item" <?php echo ($ad['link'] !== '#!') ? 'target="_blank"' : ''; ?>>
                                <img src="<?php echo esc_url($ad['image']); ?>" alt="Advertising Banner">
                            </a>
                        <?php endforeach;
                    else: ?>
                        <a href="#!" class="banners-ads__item"><img src="<?php echo get_template_directory_uri(); ?>/img/ad-test.png" alt="Ad"></a>
                        <a href="#!" class="banners-ads__item"><img src="<?php echo get_template_directory_uri(); ?>/img/ad-test.png" alt="Ad"></a>
                        <a href="#!" class="banners-ads__item"><img src="<?php echo get_template_directory_uri(); ?>/img/ad-test.png" alt="Ad"></a>
                        <a href="#!" class="banners-ads__item"><img src="<?php echo get_template_directory_uri(); ?>/img/ad-test.png" alt="Ad"></a>
                    <?php endif; ?>
                </div>

                <div class="banners-ads__footer">
                    <a href="#!" class="ad-cta">
                        Разместить рекламу 
                        <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right.svg" alt="arrow" class="cta-arrow">
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Advertisement Modal -->
    <div class="global-modal-overlay" id="adModal" data-lenis-prevent>
        <div class="global-modal global-modal--form ad-modal-v2">
            <button class="global-modal__close" id="closeAdModal">&times;</button>
            
            <div class="ad-modal__header">
                <h2 class="ad-modal__title">Добавление рекламы</h2>
                <p class="ad-modal__subtitle">Заполните данные для размещения баннера на сайте.</p>
            </div>

            <form class="ad-modal__form" id="adRequestForm">
                <div class="form-group">
                    <input type="text" name="ad_user_name" class="form-input-v2" placeholder="Имя Фамилия" required>
                </div>

                <div class="form-group">
                    <input type="url" name="ad_link" class="form-input-v2" placeholder="Ссылка (URL) на ваш сайт или соцсети" required>
                </div>

                <div class="form-row-v2">
                    <div class="form-group-v2 icon-input-v2">
                        <input type="tel" name="ad_user_phone" class="form-input-v2" placeholder="Номер телефона" required>
                        <img src="<?php echo get_template_directory_uri(); ?>/img/phone-icon.svg" alt="phone" class="field-icon-v2">
                    </div>
                    <div class="form-group-v2 icon-input-v2">
                        <input type="email" name="ad_user_email" class="form-input-v2" placeholder="Почта" required>
                        <img src="<?php echo get_template_directory_uri(); ?>/img/mail-icon.svg" alt="mail" class="field-icon-v2">
                    </div>
                </div>

                <div class="custom-select-v2" id="adSelectV2">
                    <div class="select-selected-v2">
                        <span>Тип рекламы</span>
                        <span class="select-arrow-v2"></span>
                    </div>
                    <div class="select-items-v2 select-hide-v2">
                        <div data-value="b1"><span>Рекламный баннер 1 (первый блок под "О нас")</span> <span class="dim">1662 × 1056 px</span></div>
                        <div data-value="b2"><span>Рекламный баннер 2 (промо-слайдер на главной)</span> <span class="dim">1856 × 704 px</span></div>
                        <div data-value="b3"><span>Рекламный баннер 3 (карточка в сетке Мероприятий)</span> <span class="dim">652 × 908 px</span></div>
                        <div data-value="b4"><span>Рекламный баннер 4 (нижний блок перед подвалом)</span> <span class="dim">909 × 1455 px</span></div>
                    </div>
                    <input type="hidden" name="ad_type" id="ad_type_hidden" required>
                </div>

                <div class="upload-box-v2" id="adUploadBoxV2">
                    <div class="upload-box-v2__content">
                        <div class="upload-box-v2__icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/upload.svg" alt="upload">
                        </div>
                        <div class="upload-box-v2__text">
                            <h4>Загрузить фотографий</h4>
                            <p>Перетащите или нажмите на данную область, чтобы загрузить файлы</p>
                        </div>
                    </div>
                    <input type="file" name="ad_image" id="adFileInputV2" hidden accept="image/*" required>
                </div>

                <div class="form-footer-v2">
                    <button type="submit" class="btn-submit-v2">
                        Отправить заявку &rarr;
                    </button>
                </div>
            </form>

            <!-- Success State (Hidden by default) -->
            <div class="ad-modal__success" style="display: none;">
                <div class="success-icon-v2">✓</div>
                <h3>Отправлено на модерацию</h3>
                <p>Ваша заявка на размещение рекламы получена. <br>Менеджер свяжется с вами в ближайшее время.</p>
                <button type="button" class="btn btn--black" id="closeSuccessBtnV2">Понятно</button>
            </div>
        </div>
    </div>

<?php get_footer(); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const tabBtns = document.querySelectorAll(".tab-btn");
    const heroTypeInput = document.getElementById("heroEventType");
    if (tabBtns) {
        tabBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                tabBtns.forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                if (heroTypeInput) heroTypeInput.value = btn.dataset.type;
            });
        });
    }
});
</script>
