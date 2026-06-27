<?php 
/**
 * Template Post Type: gallery_album
 */
get_header(); 

// Enqueue Spotlight Lightbox
echo '<script src="https://cdn.jsdelivr.net/npm/spotlight.js@0.7.8/dist/spotlight.bundle.js"></script>';

while (have_posts()) : the_post();
    $photos = get_post_meta(get_the_ID(), '_gh_album_photos', true);
    $drive_link = get_post_meta(get_the_ID(), '_gh_drive_link', true);
    $country = get_post_meta(get_the_ID(), '_gh_country', true);
    $city = get_post_meta(get_the_ID(), '_gh_city', true);
    $location_name = get_post_meta(get_the_ID(), '_gh_location_name', true);
    $dates = get_post_meta(get_the_ID(), '_gh_dates', true);
    $category = get_post_meta(get_the_ID(), '_gh_category', true);
    
    $cat_label = 'Турнир';
    if ($category === 'camps') $cat_label = 'Сборы';
    if ($category === 'seminars') $cat_label = 'Семинары и мастер классы';

    $location_str = implode(', ', array_filter([$country, $city]));
?>

    <main class="main-content gallery-single-page" style="padding-top: 40px; padding-bottom: 80px;">
        <div class="container container--wide">
            
            <div class="event-breadcrumbs" style="margin-bottom: 24px;">
                <a href="<?php echo esc_url(home_url('/gallery/')); ?>">Фото и видео</a> / <strong><?php the_title(); ?></strong>
            </div>

            <div class="gallery-single-hero" style="margin-bottom: 40px;">
                <div class="event-hero__tags" style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div class="event-tag event-tag--gray">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        <?php echo esc_html($cat_label); ?>
                    </div>
                </div>

                <h1 class="gallery-single__title"><?php the_title(); ?></h1>

                <div class="event-meta-list event-meta-list--row" style="margin-top: 20px;">
                    <div class="meta-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>
                            <?php echo esc_html($location_str); ?>
                            <?php if ($location_name) : ?>
                                <br><span style="color:#777; font-weight:normal; margin-top:2px; display:inline-block;"><svg style="width:14px;height:14px;margin-right:4px;vertical-align:text-bottom" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg> <?php echo esc_html($location_name); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <strong><?php echo $dates ? esc_html(date('d.m.Y', strtotime($dates))) : 'Даты не указаны'; ?></strong>
                    </div>
                </div>
            </div>

            <?php if ($drive_link) : ?>
            <div class="gallery-tabs-row" style="margin-bottom: 30px; display: flex; justify-content: flex-start;">
                <a href="<?php echo esc_url($drive_link); ?>" target="_blank" class="btn--more-photos">
                    Больше фотографий на диске →
                </a>
            </div>
            <?php endif; ?>

            <div class="masonry-grid">
                <?php 
                if (!empty($photos) && is_array($photos)) :
                    foreach ($photos as $photo_id) :
                        $img_url = wp_get_attachment_image_url($photo_id, 'full');
                        if ($img_url) :
                ?>
                        <div class="masonry-item">
                            <a href="<?php echo esc_url($img_url); ?>" class="spotlight" data-zoom="true" data-theme="dark">
                                <?php echo wp_get_attachment_image($photo_id, 'large', false, array('loading' => 'lazy')); ?>
                            </a>
                        </div>
                <?php 
                        endif;
                    endforeach; 
                else :
                    echo '<div style="grid-column: 1/-1; padding: 60px; text-align: center; color: #888; background: #f9f9f9; border-radius: 12px;">В этом альбоме пока нет фотографий.</div>';
                endif; 
                ?>
            </div>

        </div>
    </main>

<?php 
endwhile;
get_footer(); 
?>
