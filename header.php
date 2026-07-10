<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content">
	<meta name="google-site-verification" content="3YZvsjZcb8PJ-xCtCQySjaOR7f3pymHREFGE5_pYJP4" />
	
	<!-- PWA Configuration -->
	<link rel="manifest" href="<?php echo esc_url(home_url('/manifest.json')); ?>">
	<meta name="theme-color" content="#ff2d55">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri() . '/img/app-icon-192.png?v=3'); ?>">
	
	<script>
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', () => {
				navigator.serviceWorker.register('<?php echo esc_url(home_url('/service-worker.js')); ?>')
					.then(reg => console.log('Service Worker registered:', reg.scope))
					.catch(err => console.error('Service Worker registration failed:', err));
			});
		}
	</script>

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
            <div class="preloader__text notranslate" translate="no">Gymnastics<span>Hub</span></div>
        </div>
    </div>
    
    <?php if (!is_page_template('page-auth.php') && !is_page_template('verify-email.php') && !is_page_template('recovery-success.php')) : ?>
    <header class="main-header <?php echo $header_class; ?>">
        <div class="container container--wide">
            <div class="header__logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo notranslate" translate="no">
                    <svg width="220" height="35" viewBox="0 0 220 35" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                        <!-- Star -->
                        <g transform="translate(0, 0) scale(0.47)">
                            <path d="M36.6602 0L44.437 29.2244L73.3203 37.093L44.437 44.9616L36.6602 74.186L28.8834 44.9616L0 37.093L28.8834 29.2244L36.6602 0Z" fill="<?php echo $is_light_header ? '#1a1a1a' : '#ffffff'; ?>"/>
                        </g>
                        <!-- Text -->
                        <text x="47" y="25" font-family="'Raleway', sans-serif" font-weight="700" font-size="20px" fill="<?php echo $is_light_header ? '#1a1a1a' : '#ffffff'; ?>">Gymnastics<tspan fill="#ff2d55">Hub</tspan></text>
                    </svg>
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
                            $menu_types = gh_sort_event_types($menu_types);
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