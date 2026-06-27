<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="google-site-verification" content="3YZvsjZcb8PJ-xCtCQySjaOR7f3pymHREFGE5_pYJP4" />
	
    <?php wp_head(); ?>
</head>
<?php
// Determine header style based on page
$is_light_header = !is_front_page(); 
$header_class = $is_light_header ? 'main-header--light' : '';
$logo_img = $is_light_header ? 'logo-black.svg' : 'logo.svg';
$btn_class = $is_light_header ? 'btn--black' : 'btn--white';
$plus_icon = $is_light_header ? 'img/plus-black.svg' : 'img/plus.svg'; // Assuming you have a black plus for light theme
?>
<body <?php body_class('dark-theme loading'); ?>>
    <!-- Premium Preloader -->
    <div id="preloader" class="preloader">
        <div class="preloader__content">
            <div class="preloader__star">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="logo">
            </div>
            <div class="preloader__text">Gymnastics<span>Hub</span></div>
        </div>
    </div>
    
    <?php if (!is_page_template('page-auth.php') && !is_page_template('verify-email.php') && !is_page_template('recovery-success.php')) : ?>
    <header class="main-header <?php echo $header_class; ?>">
        <div class="container container--wide">
            <div class="header__logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/<?php echo $logo_img; ?>" alt="logo">
                    <span class="logo__text">Gymnastics<span>Hub</span></span>
                </a>
            </div>

            <nav class="header__nav">
                <ul class="nav-menu">
                    <li class="nav-menu__item dropdown">
                        <a href="#" class="nav-menu__link">Мероприятия <span class="arrow"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo esc_url(home_url('/events/')); ?>" class="dropdown-link">Все</a></li>
                            <?php 
                            $menu_types = get_terms(array('taxonomy' => 'event_type', 'hide_empty' => false));
                            foreach ($menu_types as $m_type) : ?>
                                <li><a href="<?php echo esc_url(home_url('/events/?event_type=' . $m_type->slug)); ?>" class="dropdown-link"><?php echo $m_type->name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-menu__item dropdown">
                        <a href="#" class="nav-menu__link">Медиа <span class="arrow"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo esc_url(home_url('/gallery/')); ?>" class="dropdown-link">Галерея</a></li>
                            <li><a href="<?php echo esc_url(home_url('/forum/')); ?>" class="dropdown-link">Форум</a></li>
                        </ul>
                    </li>
                    <li class="nav-menu__item">
                        <a href="<?php echo esc_url(home_url('/ads/')); ?>" class="nav-menu__link">Объявления</a>
                    </li>
                    <li class="nav-menu__item">
                        <a href="<?php echo esc_url(home_url('/rating/')); ?>" class="nav-menu__link">Рейтинг</a>
                    </li>
                    <li class="nav-menu__item">
                        <a href="#" class="nav-menu__link">Магазин</a>
                    </li>
                </ul>
            </nav>

            <div class="header__actions">
                <a href="<?php echo esc_url(home_url('/add-event/')); ?>" class="btn <?php echo $is_light_header ? 'btn--outline-dark' : 'btn--outline-white'; ?> btn--add">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/<?php echo ($is_light_header ? 'plus-dark.svg' : 'plus.svg'); ?>" alt="plus" onerror="this.src='<?php echo get_template_directory_uri(); ?>/img/plus.svg'">
                    Добавить мероприятие
                </a>
                <?php if (is_user_logged_in()) : 
                    $current_user = wp_get_current_user();
                    $header_avatar = get_user_meta($current_user->ID, 'gh_avatar', true);
                    if (!$header_avatar) {
                        $header_avatar = get_template_directory_uri() . '/img/' . ($is_light_header ? 'user-white.svg' : 'user_icon.svg');
                    }
                ?>
                    <a href="<?php echo esc_url(home_url('/profile/')); ?>" class="btn <?php echo $btn_class; ?> btn--login">
                        <img src="<?php echo esc_url($header_avatar); ?>" alt="user" class="btn-icon user-avatar-img" style="border-radius: 50%; width: 20px; height: 20px; object-fit: cover;"> 
                        Профиль
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/login/')); ?>" class="btn <?php echo $btn_class; ?> btn--login">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/<?php echo ($is_light_header ? 'user-white.svg' : 'user_icon.svg'); ?>" alt="user" class="btn-icon" onerror="this.src='<?php echo get_template_directory_uri(); ?>/img/user_icon.svg'"> 
                        Войти
                    </a>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" aria-label="Открыть меню">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    <?php endif; ?>