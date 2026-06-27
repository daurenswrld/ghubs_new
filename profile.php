<?php
/**
 * Template Name: Личный кабинет
 */

// Redirect to login if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_roles = $current_user->roles;
$registration_date = date('d.m.Y', strtotime($current_user->user_registered));

// Determine Role Label
$role_label = 'Пользователь';
if (in_array('gh_club', $user_roles)) $role_label = 'Клуб';
if (in_array('gh_coach', $user_roles)) $role_label = 'Тренер';
if (in_array('gh_organizer', $user_roles)) $role_label = 'Организатор';
if (in_array('administrator', $user_roles)) $role_label = 'Администратор';

get_header(); ?>

<main class="main-content profile-page">
    <!-- Simplified Profile Header -->
    <section class="profile-header" style="padding-top: 60px;">
        <div class="container container--wide">
            <div class="profile-header__content" style="margin-top: 0;">
                <?php 
                $avatar_url = get_user_meta($user_id, 'gh_avatar', true);
                if (!$avatar_url) {
                    $avatar_url = get_template_directory_uri() . '/img/user_icon.svg';
                }
                ?>
                <div class="profile-header__avatar">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar" class="user-avatar-img">
                </div>
                <div class="profile-header__info">
                    <h1 class="name"><?php echo esc_html($current_user->display_name); ?></h1>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <span class="role-badge" style="margin-bottom: 0;"><?php echo esc_html($role_label); ?></span>
                    </div>
                    <?php $user_desc = get_user_meta($user_id, 'description', true); ?>
                    <p class="profile-bio" style="font-size: 14px; color: #666; max-width: 500px; line-height: 1.5; margin-top: 8px; <?php echo empty($user_desc) ? 'display: none;' : ''; ?>"><?php echo esc_html($user_desc); ?></p>
                </div>
                <div class="profile-header__actions">
                    <button class="btn btn--outline-dark btn--pill" id="openEditProfile">Редактировать</button>
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn--outline-dark btn--pill" style="color: #ff4d4d; border-color: #ff4d4d; margin-left: 10px;">Выйти</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Profile Modal -->
    <div class="global-modal-overlay" id="editProfileModal" data-lenis-prevent>
        <div class="global-modal global-modal--form" style="max-width: 500px; max-height: 85vh; overflow-y: auto;">
            <button class="modal-close" id="closeEditProfile" aria-label="Закрыть">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>

            <h2 class="modal-title" style="margin-bottom: 24px;">Редактировать профиль</h2>

            <form class="edit-profile-form" id="editProfileForm" style="width: 100%;">
                <?php wp_nonce_field('gh-profile-nonce', 'profile_nonce'); ?>
                <!-- Avatar Upload -->
                <div class="avatar-upload-group" style="text-align: center; margin-bottom: 30px;">
                    <div class="avatar-preview" style="width: 100px; height: 100px; border-radius: 50%; background: #F2F2F2; margin: 0 auto 15px; overflow: hidden; position: relative; border: 2px solid #E0E0E0;">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="" class="user-avatar-preview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <label class="btn btn--outline-dark btn--sm" style="cursor: pointer;">
                        <span>Сменить фото</span>
                        <input type="file" name="avatar" hidden accept="image/*">
                    </label>
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">
                        <?php echo in_array('gh_club', $user_roles) ? 'Название клуба' : (in_array('gh_coach', $user_roles) ? 'Ф.И.О Тренера' : 'Имя отображения'); ?>
                        <?php if (in_array('gh_club', $user_roles) || in_array('gh_coach', $user_roles)) : ?>
                            <span style="color: #ff4d4d;">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" name="display_name" class="form-input--pill" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                </div>

                <?php if (in_array('gh_club', $user_roles) || in_array('gh_coach', $user_roles)) : ?>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Страна <span style="color: #ff4d4d;">*</span></label>
                        <input type="text" name="gh_country" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_country', true)); ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Город <span style="color: #ff4d4d;">*</span></label>
                        <input type="text" name="gh_city" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_city', true)); ?>" required>
                    </div>
                <?php endif; ?>

                <?php if (in_array('gh_club', $user_roles)) : ?>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Точный адрес (филиалы) <span style="color: #ff4d4d;">*</span></label>
                        <input type="text" name="gh_address" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_address', true)); ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Год основания клуба</label>
                        <input type="number" name="gh_foundation_year" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_foundation_year', true)); ?>">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Общее количество воспитанниц</label>
                        <input type="number" name="gh_pupils_count" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_pupils_count', true)); ?>">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Количество тренеров в штате</label>
                        <input type="number" name="gh_coaches_count" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_coaches_count', true)); ?>">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Количество подготовленных ЗМС, МСМК, МС и КМС, воспитанных в клубе</label>
                        <input type="number" name="gh_ranked_athletes_count" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_ranked_athletes_count', true)); ?>">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Главный тренер (Ф.И.О)</label>
                        <input type="text" name="gh_head_coach" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_head_coach', true)); ?>">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Действующие тренеры (Ссылки на карточки через запятую)</label>
                        <textarea name="gh_coaches_links" class="form-input--pill" style="height: 60px; padding: 10px 15px; resize: none; border-radius: 12px; width: 100%; border: 1px solid #e5e5e5; outline: none; font-size: 14px;" placeholder="https://ghubs.net/author/username1, ..."><?php echo esc_textarea(get_user_meta($user_id, 'gh_coaches_links', true)); ?></textarea>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">Спортивные залы (инфраструктура)</label>
                        <textarea name="gh_gyms" class="form-input--pill" style="height: 60px; padding: 10px 15px; resize: none; border-radius: 12px; width: 100%; border: 1px solid #e5e5e5; outline: none; font-size: 14px;" placeholder="Описание залов, адреса филиалов..."><?php echo esc_textarea(get_user_meta($user_id, 'gh_gyms', true)); ?></textarea>
                    </div>
                <?php endif; ?>

                <div class="form-group" style="margin-top: 15px;">
                    <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">
                        <?php echo in_array('gh_club', $user_roles) ? 'О клубе (Миссия и достижения)' : (in_array('gh_coach', $user_roles) ? 'О тренере (Опыт и достижения)' : 'Описание (для рейтинга)'); ?>
                        <?php if (in_array('gh_club', $user_roles) || in_array('gh_coach', $user_roles)) : ?>
                            <span style="color: #ff4d4d;">*</span>
                        <?php endif; ?>
                    </label>
                    <textarea name="description" class="form-input--pill" style="height: 100px; padding: 15px 20px; resize: none; border-radius: 20px; width: 100%; border: 1px solid #e5e5e5; outline: none; font-size: 14px;" placeholder="<?php echo in_array('gh_club', $user_roles) ? 'Кратко опишите уникальность вашего клуба, главные командные победы и условия приема детей...' : (in_array('gh_coach', $user_roles) ? 'Кратко опишите ваш тренерский стаж, достижения воспитанниц и квалификацию...' : 'Короткое описание вашего клуба или организации...'); ?>" <?php echo (in_array('gh_club', $user_roles) || in_array('gh_coach', $user_roles)) ? 'required' : ''; ?>><?php echo esc_textarea(get_user_meta($user_id, 'description', true)); ?></textarea>
                </div>

                <?php if (in_array('gh_club', $user_roles) || in_array('gh_coach', $user_roles)) : ?>
                    <div class="form-group" style="margin-top: 15px;">
                        <label style="display: block; font-size: 13px; color: #999; margin-bottom: 8px; font-weight: 500;">
                            <?php echo in_array('gh_club', $user_roles) ? 'Телефон для справок / WhatsApp админ' : 'Телефон для связи / WhatsApp'; ?>
                            <span style="color: #ff4d4d;">*</span>
                        </label>
                        <input type="text" name="gh_phone" class="form-input--pill" value="<?php echo esc_attr(get_user_meta($user_id, 'gh_phone', true)); ?>" required>
                    </div>
                <?php endif; ?>

                <div class="form-footer" style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px;">
                    <div class="profile-update-message" style="display: none; margin-bottom: 10px; font-size: 14px; text-align: center;"></div>
                    <button type="submit" class="btn btn--black btn--pill w-100">Сохранить изменения</button>
                    
                    <div class="danger-zone" style="border-top: 1px solid #F0F0F0; padding-top: 20px; margin-top: 10px;">
                        <button type="button" id="triggerDeleteAccount" class="btn btn--outline-dark btn--pill w-100" style="color: #FF4B4B; border-color: #FF4B4B;">Удалить аккаунт</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="global-modal-overlay" id="deleteAccountModal" data-lenis-prevent>
        <div class="global-modal global-modal--form" style="max-width: 450px;">
            <button class="modal-close" id="closeDeleteModal" aria-label="Закрыть">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>

            <h2 class="modal-title" style="margin-bottom: 15px; color: #FF4B4B;">Удалить аккаунт?</h2>
            <p style="font-size: 14px; color: #666; margin-bottom: 24px; line-height: 1.5;">
                Это действие необратимо. Все ваши данные будут удалены. <br>
                Чтобы подтвердить, введите ваше имя: <strong><?php echo esc_html($current_user->display_name); ?></strong>
            </p>

            <form id="deleteAccountForm">
                <input type="hidden" id="expectedName" value="<?php echo esc_attr($current_user->display_name); ?>">
                <div class="form-group">
                    <input type="text" id="confirmNameInput" placeholder="Введите имя для подтверждения" class="form-input--pill" required autocomplete="off">
                </div>
                
                <div class="profile-delete-message" style="display: none; margin-bottom: 15px; font-size: 13px; text-align: center;"></div>

                <div class="form-footer" style="margin-top: 20px;">
                    <button type="submit" id="deleteSubmitBtn" class="btn btn--pill w-100" style="background: #F0F0F0; color: #999; cursor: not-allowed;" disabled>Удалить мой аккаунт навсегда</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container container--wide">
        <!-- Basic Stats -->
        <div class="stats-row" style="grid-template-columns: repeat(2, 1fr);">
            <div class="stat-box">
                <span class="stat-box__value"><?php echo $registration_date; ?></span>
                <span class="stat-box__label">Дата регистрации</span>
            </div>
            <div class="stat-box">
                <?php 
                $topic_count = count_user_posts($user_id, 'forum_topic');
                $comment_count = get_comments(array('user_id' => $user_id, 'post_type' => 'forum_topic', 'count' => true));
                $total_forum_activity = $topic_count + $comment_count;
                ?>
                <span class="stat-box__value"><?php echo $total_forum_activity; ?></span>
                <span class="stat-box__label">Активность на форуме</span>
            </div>
        </div>

        <div class="profile-grid" style="grid-template-columns: 1fr;">
            <!-- Main Activity Column -->
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
                ?>
                    <div class="profile-card" style="margin-bottom: 30px;">
                        <h2 class="profile-card__title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 10px; vertical-align: middle;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line></svg>
                            🏢 Карточка клуба
                        </h2>
                        <div class="club-card-details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; padding: 10px 0;">
                            
                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Название клуба</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo esc_html($current_user->display_name); ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Страна, Город</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;">
                                    <?php 
                                    $loc = array_filter(array($gh_country, $gh_city));
                                    echo !empty($loc) ? esc_html(implode(', ', $loc)) : 'Не указано';
                                    ?>
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Точный адрес / филиалы</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_address ? esc_html($gh_address) : 'Не указан'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Год основания клуба</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_foundation_year ? esc_html($gh_foundation_year) : 'Не указан'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Общее количество воспитанниц</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_pupils_count ? esc_html($gh_pupils_count) : 'Не указано'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Количество тренеров в штате</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_coaches_count ? esc_html($gh_coaches_count) : 'Не указано'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Подготовлено ЗМС, МСМК, МС, КМС</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_ranked_athletes_count ? esc_html($gh_ranked_athletes_count) : 'Не указано'; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Главный тренер</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;"><?php echo $gh_head_coach ? esc_html($gh_head_coach) : 'Не указан'; ?></span>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Действующие тренеры (Ссылки на карточки)</span>
                                <span class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.5;">
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
                                        echo 'Не указаны';
                                    }
                                    ?>
                                </span>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Спортивные залы / Инфраструктура</span>
                                <div class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6; white-space: pre-line;"><?php echo $gh_gyms ? esc_html($gh_gyms) : 'Не указаны'; ?></div>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">О клубе (Миссия и достижения)</span>
                                <div class="detail-value" style="font-size: 15px; color: #1a1a1a; line-height: 1.6; white-space: pre-line;"><?php echo $user_desc ? esc_html($user_desc) : 'Не указано'; ?></div>
                            </div>

                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="detail-label" style="display: block; font-size: 13px; color: #999; margin-bottom: 4px;">Контакты (Телефон / WhatsApp)</span>
                                <span class="detail-value" style="font-size: 16px; font-weight: 600; color: #1a1a1a;">
                                    <?php if ($gh_phone) : ?>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $gh_phone)); ?>" style="color: #1a1a1a;"><?php echo esc_html($gh_phone); ?></a>
                                        <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $gh_phone)); ?>" target="_blank" style="margin-left: 10px; display: inline-flex; align-items: center; gap: 4px; color: #25d366; text-decoration: none; font-size: 13px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.003 5.324 5.328 0 11.894 0c3.18.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.57-5.328 11.894-11.893 11.894-1.996-.001-3.956-.5-5.698-1.448L0 24zm6.59-4.846c1.6.95 3.197 1.449 4.885 1.45 5.382.002 9.765-4.38 9.768-9.764.001-2.61-1.01-5.059-2.846-6.896C16.557 2.107 14.113 1.09 11.5 1.09c-5.378 0-9.761 4.38-9.764 9.762-.001 1.778.475 3.51 1.378 5.022L2.144 21.5l5.503-1.442-.5-.304zm10.705-7.185c-.302-.15-1.788-.882-2.057-.98-.267-.099-.463-.15-.658.15-.195.3-.757.953-.928 1.15-.17.199-.34.223-.642.073-.302-.15-1.272-.469-2.423-1.496-.895-.798-1.5-1.784-1.676-2.083-.176-.3-.019-.462.132-.612.135-.136.302-.35.453-.524.15-.175.2-.3.302-.5.101-.199.05-.375-.025-.524-.075-.15-.658-1.587-.902-2.174-.237-.573-.48-.494-.658-.503-.17-.008-.366-.01-.563-.01-.197 0-.518.073-.789.375-.27.3-1.03 1.008-1.03 2.46s1.057 2.85 1.205 3.05c.148.199 2.08 3.178 5.039 4.454.703.304 1.252.486 1.68.622.709.227 1.355.195 1.866.119.569-.084 1.788-.731 2.037-1.438.25-.706.25-1.313.175-1.438-.075-.125-.27-.199-.572-.35z"/></svg>
                                            WhatsApp
                                        </a>
                                    <?php else : ?>
                                        Не указаны
                                    <?php endif; ?>
                                </span>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

                <div class="profile-card">
                    <h2 class="profile-card__title">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/date.svg" alt="">
                        Последняя активность
                    </h2>
                    <div class="activity-feed">
                        <?php
                        $activities = array();

                        // 1. Registration
                        $registration_time = strtotime($current_user->user_registered);
                        $activities[] = array(
                            'title' => 'Вы успешно зарегистрировались на платформе <strong>Gymnastics Hub</strong>.',
                            'time'  => $registration_time,
                            'icon_html'  => '<img src="' . get_template_directory_uri() . '/img/mail-gray.svg" alt="">'
                        );

                        // 2. User Albums (Published and Rejected)
                        $user_albums = get_posts(array(
                            'post_type'      => 'gallery_album',
                            'post_status'    => array('publish', 'rejected'),
                            'author'         => $user_id,
                            'posts_per_page' => 15,
                        ));
                        
                        foreach ($user_albums as $album) {
                            if ($album->post_status === 'publish') {
                                $activities[] = array(
                                    'title' => 'Ваш альбом <strong>' . esc_html($album->post_title) . '</strong> успешно опубликован.',
                                    'time'  => strtotime($album->post_date),
                                    'icon_html'  => '<img src="' . get_template_directory_uri() . '/img/upload.svg" alt="">'
                                );
                            } else if ($album->post_status === 'rejected') {
                                $activities[] = array(
                                    'title' => 'Ваш альбом <strong>' . esc_html($album->post_title) . '</strong> был отклонен модератором.',
                                    'time'  => strtotime($album->post_modified),
                                    'icon_html'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e00" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'
                                );
                            }
                        }

                        // 3. User Forum Topics (Approved and Rejected)
                        $user_topics = get_posts(array(
                            'post_type'      => 'forum_topic',
                            'post_status'    => array('publish', 'rejected'),
                            'author'         => $user_id,
                            'posts_per_page' => 15,
                        ));

                        foreach ($user_topics as $topic) {
                            if ($topic->post_status === 'publish') {
                                $activities[] = array(
                                    'title' => 'Ваша тема на форуме <strong>' . esc_html($topic->post_title) . '</strong> одобрена и опубликована.',
                                    'time'  => strtotime($topic->post_date),
                                    'icon_html'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'
                                );
                            } else if ($topic->post_status === 'rejected') {
                                $activities[] = array(
                                    'title' => 'Ваша тема на форуме <strong>' . esc_html($topic->post_title) . '</strong> отклонена модератором.',
                                    'time'  => strtotime($topic->post_modified),
                                    'icon_html'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e00" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'
                                );
                            }
                        }

                        // 4. User Ads (Published, Pending, and Rejected)
                        $user_ads = get_posts(array(
                            'post_type'      => 'gh_ad',
                            'post_status'    => array('publish', 'pending', 'rejected'),
                            'author'         => $user_id,
                            'posts_per_page' => 15,
                        ));

                        foreach ($user_ads as $ad) {
                            $ad_status_msg = '';
                            $ad_icon = '';
                            
                            if ($ad->post_status === 'publish') {
                                $ad_status_msg = 'Ваше объявление <strong>' . esc_html($ad->post_title) . '</strong> успешно опубликовано.';
                                $ad_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
                            } else if ($ad->post_status === 'pending') {
                                $ad_status_msg = 'Ваше объявление <strong>' . esc_html($ad->post_title) . '</strong> отправлено на модерацию.';
                                $ad_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f39c12" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
                            } else if ($ad->post_status === 'rejected') {
                                $ad_status_msg = 'Ваше объявление <strong>' . esc_html($ad->post_title) . '</strong> было отклонено модератором.';
                                $ad_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e00" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                            }

                            if ($ad_status_msg) {
                                $activities[] = array(
                                    'title' => $ad_status_msg,
                                    'time'  => strtotime($ad->post_modified),
                                    'icon_html'  => $ad_icon
                                );
                            }
                        }

                        // 5. User Events (Published, Pending, and Rejected)
                        $user_events = get_posts(array(
                            'post_type'      => 'gh_event',
                            'post_status'    => array('publish', 'pending', 'rejected'),
                            'author'         => $user_id,
                            'posts_per_page' => 15,
                        ));

                        foreach ($user_events as $event) {
                            $event_status_msg = '';
                            $event_icon = '';
                            
                            if ($event->post_status === 'publish') {
                                $event_status_msg = 'Ваше мероприятие <strong>' . esc_html($event->post_title) . '</strong> успешно опубликовано.';
                                $event_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
                            } else if ($event->post_status === 'pending') {
                                $event_status_msg = 'Ваше мероприятие <strong>' . esc_html($event->post_title) . '</strong> отправлено на модерацию.';
                                $event_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f39c12" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
                            } else if ($event->post_status === 'rejected') {
                                $event_status_msg = 'Ваше мероприятие <strong>' . esc_html($event->post_title) . '</strong> было отклонено модератором.';
                                $event_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e00" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                            }

                            if ($event_status_msg) {
                                $activities[] = array(
                                    'title' => $event_status_msg,
                                    'time'  => strtotime($event->post_modified),
                                    'icon_html'  => $event_icon
                                );
                            }
                        }

                        // Sort by time DESC
                        usort($activities, function($a, $b) {
                            return $b['time'] - $a['time'];
                        });

                        if (!empty($activities)) {
                            foreach ($activities as $activity) {
                                ?>
                                <div class="activity-item">
                                    <div class="activity-item__icon">
                                        <?php echo $activity['icon_html']; ?>
                                    </div>
                                    <div class="activity-item__text">
                                        <?php echo $activity['title']; ?>
                                        <span class="activity-item__time"><?php echo date('d.m.Y H:i', $activity['time']); ?></span>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p style="padding: 20px; text-align: center; color: #999;">Активности пока нет.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editModal = document.getElementById('editProfileModal');
        const openBtn = document.getElementById('openEditProfile');
        const closeBtn = document.getElementById('closeEditProfile');

        <?php
        $is_club_or_coach = in_array('gh_club', $user_roles) || in_array('gh_coach', $user_roles);
        $is_incomplete = false;
        if ($is_club_or_coach) {
            $country = get_user_meta($user_id, 'gh_country', true);
            $city = get_user_meta($user_id, 'gh_city', true);
            $phone = get_user_meta($user_id, 'gh_phone', true);
            $desc = get_user_meta($user_id, 'description', true);
            $dname = $current_user->display_name;

            if (empty($country) || empty($city) || empty($phone) || empty($desc) || empty($dname)) {
                $is_incomplete = true;
            }
            if (in_array('gh_club', $user_roles)) {
                $address = get_user_meta($user_id, 'gh_address', true);
                if (empty($address)) {
                    $is_incomplete = true;
                }
            }
        }
        ?>

        const isMandatory = <?php echo $is_incomplete ? 'true' : 'false'; ?>;

        if (isMandatory && editModal) {
            editModal.classList.add('is-open');
            editModal.classList.add('mandatory-modal');
            if (closeBtn) {
                closeBtn.style.display = 'none';
            }
            if (window.lenis) window.lenis.stop();

            // Insert alert message
            const form = document.getElementById('editProfileForm');
            if (form) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'mandatory-profile-alert';
                alertDiv.style.background = '#ffebee';
                alertDiv.style.color = '#c62828';
                alertDiv.style.border = '1px solid #ffcdd2';
                alertDiv.style.padding = '15px';
                alertDiv.style.borderRadius = '12px';
                alertDiv.style.fontSize = '14px';
                alertDiv.style.marginBottom = '20px';
                alertDiv.style.fontWeight = '500';
                alertDiv.style.lineHeight = '1.4';
                alertDiv.innerText = 'Внимание! Пожалуйста, заполните все обязательные поля (*), чтобы продолжить пользование сайтом.';
                form.insertBefore(alertDiv, form.firstChild);
            }
        }

        if (openBtn && editModal) {
            openBtn.addEventListener('click', () => {
                editModal.classList.add('is-open');
                if (window.lenis) window.lenis.stop();
            });
        }

        if (closeBtn && editModal) {
            closeBtn.addEventListener('click', () => {
                if (editModal.classList.contains('mandatory-modal')) return;
                editModal.classList.remove('is-open');
                if (window.lenis) window.lenis.start();
            });
        }

        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) {
                if (editModal.classList.contains('mandatory-modal')) return;
                editModal.classList.remove('is-open');
                if (window.lenis) window.lenis.start();
            }
        });
    });
</script>

<?php get_footer(); ?>
