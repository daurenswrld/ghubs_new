<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); 
    $start_date = get_post_meta(get_the_ID(), '_event_start_date', true);
    $end_date   = get_post_meta(get_the_ID(), '_event_end_date', true);
    $location   = get_post_meta(get_the_ID(), '_event_location_text', true);
    $price      = get_post_meta(get_the_ID(), '_event_price', true);
    $reg_url    = get_post_meta(get_the_ID(), '_event_reg_url', true);
    $organizer  = get_post_meta(get_the_ID(), '_event_organizer', true);
    $photos     = get_post_meta(get_the_ID(), '_event_photos', true);
    $docs       = get_post_meta(get_the_ID(), '_event_docs', true);
    $whatsapp   = get_post_meta(get_the_ID(), '_event_whatsapp', true);
    $telegram   = get_post_meta(get_the_ID(), '_event_telegram', true);
    $instagram  = get_post_meta(get_the_ID(), '_event_instagram', true);

    $types = get_the_terms(get_the_ID(), 'event_type');
    $type_name = !empty($types) ? $types[0]->name : 'Мероприятие';

    // Registration Status logic
    $is_registration_open = false;
    if (!empty($reg_url)) {
        $today = date('Y-m-d');
        if (empty($start_date) || $today < $start_date) {
            $is_registration_open = true;
        }
    }
?>
    <main class="event-details-page">
        <div class="container container--wide">
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs">
                <a href="<?php echo esc_url(home_url('/events/')); ?>">Все мероприятия</a>
                <span class="separator">/</span>
                <span class="current"><?php the_title(); ?></span>
            </nav>

            <!-- Event Hero -->
            <section class="event-hero">
                <div class="event-hero__grid">
                    <!-- Gallery Slider -->
                    <div class="event-gallery" id="eventGallery">
                        <div class="gallery-main" style="position: relative; overflow: hidden; border-radius: 30px;">
                            <div class="slides-container" style="display: flex; transition: transform 0.5s ease;">
                                <?php if (!empty($photos) && is_array($photos)) : 
                                    foreach ($photos as $photo_id) : ?>
                                    <div class="slide" style="min-width: 100%; height: 500px;">
                                        <img src="<?php echo esc_url(wp_get_attachment_image_url($photo_id, 'full')); ?>" 
                                             alt="" style="width: 100%; height: 100%; object-fit: cover; cursor: zoom-in;" 
                                             class="lightbox-trigger" data-full="<?php echo esc_url(wp_get_attachment_image_url($photo_id, 'full')); ?>">
                                    </div>
                                    <?php endforeach; 
                                else : ?>
                                    <div class="slide" style="min-width: 100%; height: 500px;">
                                        <?php if (has_post_thumbnail()) : the_post_thumbnail('full', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                        <?php else : ?>
                                            <img src="<?php echo get_template_directory_uri(); ?>/img/card.png" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($photos) && count($photos) > 1) : ?>
                            <button class="gallery-nav prev" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.3); border: none; width: 44px; height: 44px; border-radius: 50%; color: #fff; cursor: pointer;">&larr;</button>
                            <button class="gallery-nav next" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.3); border: none; width: 44px; height: 44px; border-radius: 50%; color: #fff; cursor: pointer;">&rarr;</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Event Info -->
                    <div class="event-info">
                        <div class="event-tags">
                            <span class="tag tag--gray">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/cup.svg" alt="icon"> <?php echo esc_html($type_name); ?>
                            </span>
                            <?php if ($is_registration_open) : ?>
                                <span class="tag tag--success-light">Идет регистрация</span>
                            <?php endif; ?>
                        </div>

                        <h1 class="event-title"><?php the_title(); ?></h1>

                        <div class="event-details-list">
                            <div class="detail-item">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/geo.svg" alt="icon" class="detail-icon">
                                <div class="detail-content">
                                    <span class="detail-value"><?php echo esc_html($location); ?></span>
                                </div>
                            </div>
                            <div class="detail-item detail-item--date">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/date.svg" alt="icon" class="detail-icon">
                                <div class="detail-content">
                                    <span class="detail-value underline"><?php echo gh_format_event_date($start_date, $end_date); ?></span>
                                </div>
                            </div>
                            <?php if ($price) : ?>
                            <div class="detail-item">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/cup.svg" alt="icon" class="detail-icon">
                                <div class="detail-content">
                                    <span class="detail-value"><?php echo esc_html($price); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="event-actions">
                            <?php if (!empty($reg_url)) : ?>
                                <a href="<?php echo esc_url($reg_url); ?>" class="btn btn--black" target="_blank">
                                   Зарегистрироваться
                                </a>
                            <?php endif; ?>
                            <button class="btn btn--outline-dark btn--share" onclick="if(navigator.share){navigator.share({title:'<?php echo esc_js(get_the_title()); ?>',url:window.location.href})}">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/share.svg" alt="share"> Поделиться
                            </button>
                        </div>


                    </div>
                </div>
            </section>

            <!-- Event Body -->
            <section class="event-body">
                <div class="event-body__main">
                    <div class="about-block">
                        <h2 class="section-title">О мероприятии</h2>
                        <div class="about-text">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Discussion Button -->
                    <?php if (function_exists('gh_get_event_discussion_url')) : ?>
                    <a href="<?php echo gh_get_event_discussion_url(get_the_ID()); ?>" class="discussion-btn">
                        <div class="discussion-btn__left">
                            <span class="discussion-btn__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </span>
                            Обсуждение данного мероприятия на форуме
                        </div>
                        <svg class="discussion-btn__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                    <?php endif; ?>

                    <?php 
                    $docs_array = is_array($docs) ? $docs : (!empty($docs) ? array($docs) : array());
                    if (!empty($docs_array)) : ?>
                    <div class="docs-block" style="margin-top: 40px;">
                        <h2 class="section-title">Документы</h2>
                        <div class="docs-grid">
                            <?php 
                            foreach ($docs_array as $doc_id) : 
                                if (empty($doc_id)) continue;
                                $file_url  = wp_get_attachment_url($doc_id);
                                if (!$file_url) continue;
                                
                                $file_name = get_the_title($doc_id);
                                $file_ext  = pathinfo($file_url, PATHINFO_EXTENSION);
                                $file_path = get_attached_file($doc_id);
                                $file_size = ($file_path && file_exists($file_path)) ? size_format(filesize($file_path)) : '—';
                            ?>
                            <div class="doc-card">
                                <div class="doc-card__icon">
                                    <svg viewBox="0 0 24 24" fill="#E53935" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </div>
                                <div class="doc-card__info">
                                    <span class="doc-card__title"><?php echo esc_html($file_name); ?></span>
                                    <span class="doc-card__size"><?php echo esc_html(strtoupper($file_ext)); ?> • <?php echo esc_html($file_size); ?></span>
                                    <a href="<?php echo esc_url($file_url); ?>" class="doc-card__link" target="_blank">
                                        Скачать 
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <aside class="event-body__sidebar">
                    <div class="org-sidebar-widget">
                        <h3 class="widget-title">Организатор</h3>
                        <div class="org-card">
                            <div class="org-header">
                                <div class="org-avatar">
                                    <?php 
                                    $author_id = get_the_author_meta('ID');
                                    $custom_avatar = get_user_meta($author_id, 'gh_avatar', true);
                                    if ($custom_avatar) : ?>
                                        <img src="<?php echo esc_url($custom_avatar); ?>" alt="Avatar" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                                    <?php else : ?>
                                        <?php echo get_avatar($author_id, 64); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="org-info">
                                    <h4 class="org-name"><?php echo esc_html($organizer ?: get_the_author()); ?></h4>
                                    <span class="org-verified">
                                        <span class="dot"></span> Активный организатор
                                    </span>
                                </div>
                            </div>

                            <?php if ($whatsapp || $telegram || $instagram) : ?>
                            <div class="org-socials" style="margin-top: 20px; display: flex; gap: 12px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                                <?php if ($whatsapp) : 
                                    $wa_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp);
                                ?>
                                    <a href="<?php echo esc_url($wa_link); ?>" target="_blank" title="WhatsApp" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f9f9f9; border-radius: 10px; transition: 0.3s;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.435 5.63 1.435h.008c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" fill="#25D366"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ($telegram) : 
                                    $tg_link = "https://t.me/" . str_replace('@', '', $telegram);
                                ?>
                                    <a href="<?php echo esc_url($tg_link); ?>" target="_blank" title="Telegram" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f9f9f9; border-radius: 10px; transition: 0.3s;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.18l-1.92 9.06c-.14.64-.52.8-.14.36l-2.92-2.15-1.41 1.36c-.16.16-.29.29-.59.29l.21-3.02 5.5-4.97c.24-.21-.05-.33-.37-.12l-6.8 4.28-2.93-.92c-.64-.2-.65-.64.13-.95l11.44-4.41c.53-.2.99.11.8.85z" fill="#0088cc"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ($instagram) : 
                                    $inst_link = "https://instagram.com/" . str_replace('@', '', $instagram);
                                ?>
                                    <a href="<?php echo esc_url($inst_link); ?>" target="_blank" title="Instagram" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f9f9f9; border-radius: 10px; transition: 0.3s;">
                                        <svg id="instagram" fill="#D93275" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 169.063 169.063" style="enable-background:new 0 0 169.063 169.063;"
                                        xml:space="preserve">
                                        <g>
                                            <path d="M122.406,0H46.654C20.929,0,0,20.93,0,46.655v75.752c0,25.726,20.929,46.655,46.654,46.655h75.752
                                                                c25.727,0,46.656-20.93,46.656-46.655V46.655C169.063,20.93,148.133,0,122.406,0z M154.063,122.407
                                                                c0,17.455-14.201,31.655-31.656,31.655H46.654C29.2,154.063,15,139.862,15,122.407V46.655C15,29.201,29.2,15,46.654,15h75.752
                                                                c17.455,0,31.656,14.201,31.656,31.655V122.407z" />
                                            <path d="M84.531,40.97c-24.021,0-43.563,19.542-43.563,43.563c0,24.02,19.542,43.561,43.563,43.561s43.563-19.541,43.563-43.561
                                                                C128.094,60.512,108.552,40.97,84.531,40.97z M84.531,113.093c-15.749,0-28.563-12.812-28.563-28.561
                                                                c0-15.75,12.813-28.563,28.563-28.563s28.563,12.813,28.563,28.563C113.094,100.281,100.28,113.093,84.531,113.093z" />
                                            <path d="M129.921,28.251c-2.89,0-5.729,1.17-7.77,3.22c-2.051,2.04-3.23,4.88-3.23,7.78c0,2.891,1.18,5.73,3.23,7.78
                                                                c2.04,2.04,4.88,3.22,7.77,3.22c2.9,0,5.73-1.18,7.78-3.22c2.05-2.05,3.22-4.89,3.22-7.78c0-2.9-1.17-5.74-3.22-7.78
                                                                C135.661,29.421,132.821,28.251,129.921,28.251z" />
                                        </g>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </main>

    <div id="adLightbox" class="lightbox">
        <span class="lightbox__close">&times;</span>
        <img class="lightbox__content" id="lightboxImg">
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.slides-container');
    const slides = document.querySelectorAll('.slide');
    const prev = document.querySelector('.gallery-nav.prev');
    const next = document.querySelector('.gallery-nav.next');
    let idx = 0;

    function move(n) {
        if (!container) return;
        idx += n;
        if (idx < 0) idx = slides.length - 1;
        if (idx >= slides.length) idx = 0;
        container.style.transform = `translateX(-${idx * 100}%)`;
    }

    if (next) next.addEventListener('click', () => move(1));
    if (prev) prev.addEventListener('click', () => move(-1));

    // Lightbox
    const lightbox = document.getElementById('adLightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const triggers = document.querySelectorAll('.lightbox-trigger');

    triggers.forEach(t => {
        t.addEventListener('click', () => {
            lightboxImg.src = t.dataset.full;
            lightbox.classList.add('active');
        });
    });

    if (lightbox) {
        lightbox.addEventListener('click', () => lightbox.classList.remove('active'));
    }
});
</script>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
