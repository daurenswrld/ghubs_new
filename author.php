<?php
/**
 * Template Name: Публичный профиль
 */

$author = get_queried_object();
if (!$author || !isset($author->ID)) {
    // Fallback if not loaded via author query
    global $wp_query;
    $author = $wp_query->get_queried_object();
}

if (!$author || !isset($author->ID)) {
    wp_redirect(home_url());
    exit;
}

$user_id = $author->ID;
$user_roles = $author->roles;
$registration_date = date('d.m.Y', strtotime($author->user_registered));

// Determine Role Label
$role_label = 'Пользователь';
if (in_array('gh_club', $user_roles)) $role_label = 'Клуб';
if (in_array('gh_coach', $user_roles)) $role_label = 'Тренер';
if (in_array('gh_organizer', $user_roles)) $role_label = 'Организатор';
if (in_array('administrator', $user_roles)) $role_label = 'Администратор';

$avatar_url = get_user_meta($user_id, 'gh_avatar', true);
if (!$avatar_url) {
    $avatar_url = get_template_directory_uri() . '/img/user_icon.svg';
}

get_header(); ?>

<main class="main-content profile-page">
    <!-- Simplified Profile Header -->
    <section class="profile-header" style="padding-top: 60px; padding-bottom: 20px;">
        <div class="container container--wide">
            <div class="profile-header__content" style="margin-top: 0; display: flex; align-items: center; gap: 30px;">
                <div class="profile-header__avatar" style="flex-shrink: 0; width: 120px; height: 120px; border-radius: 50%; overflow: hidden; border: 2px solid #E0E0E0;">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar" class="user-avatar-img" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="profile-header__info">
                    <h1 class="name" style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;"><?php echo esc_html($author->display_name); ?></h1>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <span class="role-badge" style="margin-bottom: 0; background: #f5f5f5; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; color: #c29661;"><?php echo esc_html($role_label); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container container--wide" style="margin-top: 20px; margin-bottom: 60px;">
        <div class="profile-grid" style="grid-template-columns: 1fr;">
            <div class="profile-main">
                
                <?php if (in_array('gh_club', $user_roles)) : 
                    $gh_country = get_user_meta($user_id, 'gh_country', true);
                    $gh_city = get_user_meta($user_id, 'gh_city', true);
                    $gh_address = get_user_meta($user_id, 'gh_address', true);
                    $gh_foundation_year = get_user_meta($user_id, 'gh_foundation_year', true);
                    $gh_pupils_count = get_user_meta($user_id, 'gh_pupils_count', true);
                    $gh_coaches_count = get_user_meta($user_id, 'gh_coaches_count', true);
                    $gh_ranked_athletes_count = get_user_meta($user_id, 'gh_ranked_athletes_count', true);
                    $gh_head_coach = get_user_meta($user_id, 'gh_head_coach', true);
                    $gh_coaches_links = get_user_meta($user_id, 'gh_coaches_links', true);
                    $gh_gyms = get_user_meta($user_id, 'gh_gyms', true);
                    $gh_phone = get_user_meta($user_id, 'gh_phone', true);
                    $user_desc = get_user_meta($user_id, 'description', true);
                ?>
                    <!-- Club Detailed Card -->
                    <div class="profile-card" style="background: #ffffff; border-radius: 24px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                        <h2 class="profile-card__title" style="font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 24px; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c29661" stroke-width="2" style="margin-right: 12px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line></svg>
                            Карточка клуба
                        </h2>
                        
                        <div class="club-card-details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                            
                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Название клуба</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo esc_html($author->display_name); ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Страна, Город</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;">
                                    <?php 
                                    $loc = array_filter(array($gh_country, $gh_city));
                                    echo !empty($loc) ? esc_html(implode(', ', $loc)) : 'Не указано';
                                    ?>
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Точный адрес / филиалы</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_address ? esc_html($gh_address) : 'Не указан'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Год основания</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_foundation_year ? esc_html($gh_foundation_year) : 'Не указан'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Общее количество воспитанниц</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_pupils_count ? esc_html($gh_pupils_count) : 'Не указано'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Количество тренеров в штате</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_coaches_count ? esc_html($gh_coaches_count) : 'Не указано'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Подготовлено ЗМС, МСМК, МС, КМС</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_ranked_athletes_count ? esc_html($gh_ranked_athletes_count) : 'Не указано'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Главный тренер</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_head_coach ? esc_html($gh_head_coach) : 'Не указан'; ?></span>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Действующие тренеры</span>
                                <span class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6;">
                                    <?php 
                                    if ($gh_coaches_links) {
                                        $links = array_map('trim', explode(',', $gh_coaches_links));
                                        $link_html = array();
                                        foreach ($links as $link) {
                                            if (filter_var($link, FILTER_VALIDATE_URL)) {
                                                $link_html[] = '<a href="' . esc_url($link) . '" target="_blank" style="color: #c29661; text-decoration: underline; font-weight: 500;">' . esc_html($link) . '</a>';
                                            } else {
                                                $link_html[] = esc_html($link);
                                            }
                                        }
                                        echo implode(', ', $link_html);
                                    } else {
                                        echo '<span style="color: #999; font-style: italic;">Не указаны</span>';
                                    }
                                    ?>
                                </span>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Спортивные залы / Инфраструктура</span>
                                <div class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6; white-space: pre-line;">
                                    <?php echo $gh_gyms ? esc_html($gh_gyms) : '<span style="color: #999; font-style: italic;">Не указаны</span>'; ?>
                                </div>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">О клубе (Миссия и достижения)</span>
                                <div class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6; white-space: pre-line;">
                                    <?php echo $user_desc ? esc_html($user_desc) : '<span style="color: #999; font-style: italic;">Не указано</span>'; ?>
                                </div>
                            </div>

                            <?php if ($gh_phone) : ?>
                                <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                                    <div>
                                        <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Контакты</span>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $gh_phone)); ?>" style="font-size: 18px; font-weight: 700; color: #1a1a1a; text-decoration: none;"><?php echo esc_html($gh_phone); ?></a>
                                    </div>
                                    <div style="display: flex; gap: 12px;">
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $gh_phone)); ?>" class="btn btn--outline-dark btn--pill btn--sm" style="display: inline-flex; align-items: center; gap: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                            Позвонить
                                        </a>
                                        <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $gh_phone)); ?>" target="_blank" class="btn btn--pill btn--sm" style="background: #25d366; color: #fff; display: inline-flex; align-items: center; gap: 8px; border: none;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.003 5.324 5.328 0 11.894 0c3.18.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.57-5.328 11.894-11.893 11.894-1.996-.001-3.956-.5-5.698-1.448L0 24zm6.59-4.846c1.6.95 3.197 1.449 4.885 1.45 5.382.002 9.765-4.38 9.768-9.764.001-2.61-1.01-5.059-2.846-6.896C16.557 2.107 14.113 1.09 11.5 1.09c-5.378 0-9.761 4.38-9.764 9.762-.001 1.778.475 3.51 1.378 5.022L2.144 21.5l5.503-1.442-.5-.304zm10.705-7.185c-.302-.15-1.788-.882-2.057-.98-.267-.099-.463-.15-.658.15-.195.3-.757.953-.928 1.15-.17.199-.34.223-.642.073-.302-.15-1.272-.469-2.423-1.496-.895-.798-1.5-1.784-1.676-2.083-.176-.3-.019-.462.132-.612.135-.136.302-.35.453-.524.15-.175.2-.3.302-.5.101-.199.05-.375-.025-.524-.075-.15-.658-1.587-.902-2.174-.237-.573-.48-.494-.658-.503-.17-.008-.366-.01-.563-.01-.197 0-.518.073-.789.375-.27.3-1.03 1.008-1.03 2.46s1.057 2.85 1.205 3.05c.148.199 2.08 3.178 5.039 4.454.703.304 1.252.486 1.68.622.709.227 1.355.195 1.866.119.569-.084 1.788-.731 2.037-1.438.25-.706.25-1.313.175-1.438-.075-.125-.27-.199-.572-.35z"/></svg>
                                            Написать в WhatsApp
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php elseif (in_array('gh_coach', $user_roles)) : 
                    $gh_country = get_user_meta($user_id, 'gh_country', true);
                    $gh_city = get_user_meta($user_id, 'gh_city', true);
                    $gh_phone = get_user_meta($user_id, 'gh_phone', true);
                    $user_desc = get_user_meta($user_id, 'description', true);
                ?>
                    <!-- Coach Detailed Card -->
                    <div class="profile-card" style="background: #ffffff; border-radius: 24px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                        <h2 class="profile-card__title" style="font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 24px; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c29661" stroke-width="2" style="margin-right: 12px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Карточка тренера
                        </h2>
                        
                        <div class="club-card-details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                            
                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Ф.И.О Тренера</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo esc_html($author->display_name); ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Страна, Город</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;">
                                    <?php 
                                    $loc = array_filter(array($gh_country, $gh_city));
                                    echo !empty($loc) ? esc_html(implode(', ', $loc)) : 'Не указано';
                                    ?>
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Дата регистрации</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo esc_html($registration_date); ?></span>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">О тренере (Опыт и достижения)</span>
                                <div class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6; white-space: pre-line;">
                                    <?php echo $user_desc ? esc_html($user_desc) : '<span style="color: #999; font-style: italic;">Не указано</span>'; ?>
                                </div>
                            </div>

                            <?php if ($gh_phone) : ?>
                                <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                                    <div>
                                        <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Контакты</span>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $gh_phone)); ?>" style="font-size: 18px; font-weight: 700; color: #1a1a1a; text-decoration: none;"><?php echo esc_html($gh_phone); ?></a>
                                    </div>
                                    <div style="display: flex; gap: 12px;">
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $gh_phone)); ?>" class="btn btn--outline-dark btn--pill btn--sm" style="display: inline-flex; align-items: center; gap: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                            Позвонить
                                        </a>
                                        <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $gh_phone)); ?>" target="_blank" class="btn btn--pill btn--sm" style="background: #25d366; color: #fff; display: inline-flex; align-items: center; gap: 8px; border: none;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.003 5.324 5.328 0 11.894 0c3.18.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.57-5.328 11.894-11.893 11.894-1.996-.001-3.956-.5-5.698-1.448L0 24zm6.59-4.846c1.6.95 3.197 1.449 4.885 1.45 5.382.002 9.765-4.38 9.768-9.764.001-2.61-1.01-5.059-2.846-6.896C16.557 2.107 14.113 1.09 11.5 1.09c-5.378 0-9.761 4.38-9.764 9.762-.001 1.778.475 3.51 1.378 5.022L2.144 21.5l5.503-1.442-.5-.304zm10.705-7.185c-.302-.15-1.788-.882-2.057-.98-.267-.099-.463-.15-.658.15-.195.3-.757.953-.928 1.15-.17.199-.34.223-.642.073-.302-.15-1.272-.469-2.423-1.496-.895-.798-1.5-1.784-1.676-2.083-.176-.3-.019-.462.132-.612.135-.136.302-.35.453-.524.15-.175.2-.3.302-.5.101-.199.05-.375-.025-.524-.075-.15-.658-1.587-.902-2.174-.237-.573-.48-.494-.658-.503-.17-.008-.366-.01-.563-.01-.197 0-.518.073-.789.375-.27.3-1.03 1.008-1.03 2.46s1.057 2.85 1.205 3.05c.148.199 2.08 3.178 5.039 4.454.703.304 1.252.486 1.68.622.709.227 1.355.195 1.866.119.569-.084 1.788-.731 2.037-1.438.25-.706.25-1.313.175-1.438-.075-.125-.27-.199-.572-.35z"/></svg>
                                            Написать в WhatsApp
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php else : ?>
                    <!-- Generic Public Card -->
                    <div class="profile-card" style="background: #ffffff; border-radius: 24px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                        <h2 class="profile-card__title" style="font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 24px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                            Информация о пользователе
                        </h2>
                        <div class="club-card-details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Имя</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo esc_html($author->display_name); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Роль</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #c29661;"><?php echo esc_html($role_label); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Дата регистрации</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo esc_html($registration_date); ?></span>
                            </div>
                            <?php 
                            $user_desc = get_user_meta($user_id, 'description', true);
                            if ($user_desc) : ?>
                                <div class="detail-item" style="grid-column: 1 / -1; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                                    <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Описание</span>
                                    <div class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6; white-space: pre-line;"><?php echo esc_html($user_desc); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- User activity or posts list if any -->
                <?php
                $albums = get_posts(array(
                    'post_type' => 'gallery_album',
                    'author' => $user_id,
                    'posts_per_page' => 6
                ));
                if (!empty($albums)) : ?>
                    <div class="profile-card" style="background: #ffffff; border-radius: 24px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                        <h3 style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px;">Альбомы пользователя</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                            <?php foreach ($albums as $album) : 
                                $thumb = get_the_post_thumbnail_url($album->ID, 'medium') ?: get_template_directory_uri() . '/img/default-placeholder.png';
                            ?>
                                <a href="<?php echo esc_url(get_permalink($album->ID)); ?>" style="display: block; text-decoration: none; color: inherit; border-radius: 12px; overflow: hidden; border: 1px solid #eee;">
                                    <div style="height: 150px; background: url('<?php echo esc_url($thumb); ?>') center/cover no-repeat;"></div>
                                    <div style="padding: 12px;">
                                        <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 4px;"><?php echo esc_html($album->post_title); ?></h4>
                                        <span style="font-size: 12px; color: #999;"><?php echo get_the_date('', $album->ID); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
