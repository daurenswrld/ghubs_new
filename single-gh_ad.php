<?php get_header(); ?>

    <?php if (have_posts()) : while (have_posts()) : the_post(); 
        $price = get_post_meta(get_the_ID(), '_ad_price', true);
        $phone = get_post_meta(get_the_ID(), '_ad_phone', true);
        $email = get_post_meta(get_the_ID(), '_ad_email', true);
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');
        if (!$thumb) $thumb = get_template_directory_uri() . '/img/card.png';
    ?>
    <main class="main-content ad-single">
        <div class="container container--wide">
            <nav class="breadcrumbs">
                <a href="<?php echo esc_url(home_url('/ads/')); ?>">Все объявления</a>
                <span>/</span>
                <span class="current"><?php the_title(); ?></span>
            </nav>

            <div class="ad-main-grid">
                <div class="ad-slider">
                    <?php 
                    $photos = get_post_meta(get_the_ID(), '_ad_photos', true);
                    if (!empty($photos) && is_array($photos)) : ?>
                        <div class="ad-gallery-main" style="position: relative; margin-bottom: 20px; overflow: hidden; border-radius: 30px;">
                            <div class="ad-slides-container" style="display: flex; transition: transform 0.5s ease; width: 100%;">
                                <?php foreach ($photos as $photo_id) : ?>
                                    <div class="ad-slide" style="min-width: 100%; height: 500px;">
                                        <img src="<?php echo esc_url(wp_get_attachment_image_url($photo_id, 'full')); ?>" 
                                             alt="" 
                                             class="lightbox-trigger"
                                             data-full="<?php echo esc_url(wp_get_attachment_image_url($photo_id, 'full')); ?>"
                                             style="width: 100%; height: 100%; object-fit: cover; cursor: zoom-in;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Slider Nav -->
                            <?php if (count($photos) > 1) : ?>
                            <button class="slider-arrow slider-arrow--prev" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,0.3); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button class="slider-arrow slider-arrow--next" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,0.3); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if (count($photos) > 1) : ?>
                        <div class="ad-gallery-thumbs" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
                            <?php foreach ($photos as $index => $photo_id) : ?>
                                <div class="thumb-item" data-index="<?php echo $index; ?>" style="flex: 0 0 80px; height: 80px; border-radius: 12px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: 0.3s;">
                                    <img src="<?php echo esc_url(wp_get_attachment_image_url($photo_id, 'thumbnail')); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title(); ?>" class="lightbox-trigger" data-full="<?php echo esc_url($thumb); ?>" style="width: 100%; border-radius: 30px; object-fit: cover; cursor: zoom-in;">
                    <?php endif; ?>
                </div>

                <!-- Lightbox Markup -->
                <div id="adLightbox" class="lightbox">
                    <span class="lightbox__close">&times;</span>
                    <img class="lightbox__content" id="lightboxImg">
                </div>

                <div class="ad-info">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                    
                    <div class="info-list">
                        <div class="contact-group" style="margin-top: 0; background: #f9f9f9; padding: 25px; border-radius: 20px;">
                            <h4 style="margin-bottom: 15px;">Контакты продавца</h4>
                            <?php if ($phone) : ?>
                            <div class="info-item" style="margin-bottom: 10px;">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/phone-gray.svg" alt="Phone">
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" style="color: inherit; text-decoration: none; font-weight: 500;"><?php echo esc_html($phone); ?></a>
                            </div>
                            <?php endif; ?>
                            <?php if ($email) : ?>
                            <div class="info-item">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/mail-gray.svg" alt="Email">
                                <a href="mailto:<?php echo esc_attr($email); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html($email); ?></a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button class="share-btn" onclick="if(navigator.share){navigator.share({title: '<?php echo esc_js(get_the_title()); ?>', url: window.location.href})}" style="margin-top: 30px;">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/share.svg" alt="Share">
                        Поделиться
                    </button>
                </div>
            </div>

            <section class="ad-description" style="margin-top: 60px;">
                <h2 style="margin-bottom: 20px;">Описание</h2>
                <div class="desc-content" style="font-size: 18px; line-height: 1.6; color: #444;">
                    <?php the_content(); ?>
                </div>
            </section>
        </div>
    </main>
    <?php endwhile; endif; ?>

<style>
    .ad-main-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 60px;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .ad-main-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
    }
    .thumb-item {
        opacity: 0.6;
        border: 2px solid transparent;
        transition: 0.3s;
    }
    .thumb-item.active {
        opacity: 1;
        border-color: #1a1a1a !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.ad-slides-container');
    const slides = document.querySelectorAll('.ad-slide');
    const thumbs = document.querySelectorAll('.thumb-item');
    const prevBtn = document.querySelector('.slider-arrow--prev');
    const nextBtn = document.querySelector('.slider-arrow--next');
    
    const lightbox = document.getElementById('adLightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxClose = document.querySelector('.lightbox__close');
    const triggers = document.querySelectorAll('.lightbox-trigger');

    let currentIndex = 0;

    function updateSlider(index) {
        if (!container || slides.length === 0) return;

        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        
        currentIndex = index;
        container.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Update thumbs
        thumbs.forEach((t, i) => {
            if (i === currentIndex) {
                t.classList.add('active');
            } else {
                t.classList.remove('active');
            }
        });
    }

    if (nextBtn && prevBtn) {
        nextBtn.addEventListener('click', () => updateSlider(currentIndex + 1));
        prevBtn.addEventListener('click', () => updateSlider(currentIndex - 1));
    }

    if (thumbs.length > 0) {
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                updateSlider(parseInt(thumb.dataset.index));
            });
        });
        // Initialize
        updateSlider(0);
    }

    // Lightbox Logic
    if (triggers.length > 0 && lightbox && lightboxImg) {
        triggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                lightboxImg.src = trigger.dataset.full;
                lightbox.classList.add('active');
                if (window.lenis) window.lenis.stop();
            });
        });

        const closeLightbox = () => {
            lightbox.classList.remove('active');
            if (window.lenis) window.lenis.start();
        };

        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                closeLightbox();
            }
        });
    }
});
</script>

<?php get_footer(); ?>
