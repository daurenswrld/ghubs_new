<?php
/**
 * Gymnastics Hub Theme Functions
 */

require_once get_template_directory() . '/inc/admin-users.php';
require_once get_template_directory() . '/inc/admin-interface.php';
require_once get_template_directory() . '/inc/ajax-auth.php';
require_once get_template_directory() . '/inc/smtp-config.php';
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/profanity-filter.php';
require_once get_template_directory() . '/inc/image-handler.php';
require_once get_template_directory() . '/inc/cpt-albums.php';
require_once get_template_directory() . '/inc/ajax-gallery.php';
require_once get_template_directory() . '/inc/cpt-forum.php';
require_once get_template_directory() . '/inc/theme-settings.php';
require_once get_template_directory() . '/inc/ad-requests.php';
require_once get_template_directory() . '/inc/cpt-ads.php';
require_once get_template_directory() . '/inc/cpt-events.php';
require_once get_template_directory() . '/inc/ajax-ai.php';
require_once get_template_directory() . '/inc/ajax-rating.php';

/**
 * Theme Setup
 */
function gymnastics_hub_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'gymnastics_hub_setup');

/**
 * Custom Title Tag parts
 */
function gymnastics_hub_custom_title($title) {
    if (is_front_page()) {
        $title['title'] = 'Gymnastics Hub';
        $title['tagline'] = 'Спортивные мероприятия по художественной гимнастике по всему миру';
    } elseif (is_singular('gh_event')) {
        $city = get_post_meta(get_the_ID(), '_event_location_city', true);
        if ($city) {
            $title['title'] .= ' — ' . $city;
        }
    } elseif (is_post_type_archive('gh_event')) {
        $title['title'] = 'Все спортивные мероприятия';
    }
    
    return $title;
}
add_filter('document_title_parts', 'gymnastics_hub_custom_title');

/**
 * Disable the emoji's
 */
function gh_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'gh_disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'gh_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'gh_disable_emojis' );

function gh_disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

function gh_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' == $relation_type ) {
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2.2.1/svg/' );
        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }
    return $urls;
}

/**
 * Allow SVG Uploads
 */
function gh_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'gh_mime_types');

/**
 * Proxy for Namaztimes.kz API to avoid CORS issues
 */
function gh_ajax_proxy_namaztimes() {
    $endpoint = isset($_GET['endpoint']) ? sanitize_text_field($_GET['endpoint']) : '';
    $id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';
    
    if ($endpoint !== 'country' && $endpoint !== 'city') {
        wp_send_json_error('Invalid endpoint');
    }
    
    $url = "https://namaztimes.kz/ru/api/{$endpoint}?type=json";
    if ($id) {
        $url .= "&id={$id}";
    }
    
    $response = wp_remote_get($url, array(
        'timeout'    => 15,
        'sslverify'  => false, // Disable SSL verify for local dev environments
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ));
    
    if (is_wp_error($response)) {
        wp_send_json_error('Network error: ' . $response->get_error_message());
    }
    
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($code !== 200) {
        wp_send_json_error('API returned status code ' . $code);
    }
    
    // Check if body is valid JSON
    if (empty($body) || strpos($body, '<!DOCTYPE') !== false) {
        wp_send_json_error('API returned HTML instead of JSON');
    }

    header('Content-Type: application/json');
    echo $body;
    exit;
}
add_action('wp_ajax_gh_proxy_nt', 'gh_ajax_proxy_namaztimes');
add_action('wp_ajax_nopriv_gh_proxy_nt', 'gh_ajax_proxy_namaztimes');

// Custom Login Styles
function gymnastics_hub_login_styles() {
    wp_enqueue_style('custom-login', get_template_directory_uri() . '/assets/css/login-style.css');
}
add_action('login_enqueue_scripts', 'gymnastics_hub_login_styles');

// Custom Admin Styles
function gymnastics_hub_admin_styles() {
    wp_enqueue_style('custom-admin', get_template_directory_uri() . '/assets/css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'gymnastics_hub_admin_styles');

// Change Login Logo URL
add_filter('login_headerurl', function() { 
    return home_url(); 
});

// Change Login Logo Title
add_filter('login_headertext', function() { 
    return 'GymnasticsHub'; 
});

// Custom Admin Footer Text
add_filter('admin_footer_text', function() {
    return 'Панель управления <a href="https://ghubs.net" target="_blank">GymnasticsHub</a>. Разработано с любовью к спорту.';
});

// Enqueue Styles and Scripts
function gymnastics_hub_scripts_styles() {
    // Fonts
    wp_enqueue_style('gymnastics-hub-fonts', 'https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Styles (Cache-busting with filemtime)
    $main_css_path = get_stylesheet_directory() . '/style.css';
    $main_css_ver = file_exists($main_css_path) ? filemtime($main_css_path) : '1.1.0';
    wp_enqueue_style('gymnastics-hub-main-style', get_stylesheet_uri(), array(), $main_css_ver);
    
    $custom_css_path = get_template_directory() . '/css/style.css';
    $custom_css_ver = file_exists($custom_css_path) ? filemtime($custom_css_path) : '1.1.0';
    wp_enqueue_style('gymnastics-hub-custom-style', get_template_directory_uri() . '/css/style.css', array(), $custom_css_ver);
    
    // Scripts
    wp_enqueue_script('gymnastics-hub-lenis', 'https://unpkg.com/lenis@1.1.20/dist/lenis.min.js', array(), '1.1.20', true);
    
    // Do not load AI Assistant on Auth pages
    if (!is_page_template('page-auth.php') && !is_page_template('verify-email.php') && !is_page_template('recovery-success.php')) {
        wp_enqueue_script('gymnastics-hub-stefa', get_template_directory_uri() . '/js/stefa.js', array(), '1.0.7', true);
    }
    
    wp_enqueue_script('gymnastics-hub-main', get_template_directory_uri() . '/js/main.js', array('gymnastics-hub-lenis'), '1.0.7', true);
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // Localize script to pass template directory URI and AJAX data
    wp_localize_script('gymnastics-hub-main', 'themeData', array(
        'templateUri' => get_template_directory_uri(),
        'homeUrl'     => home_url(),
        'ajax_url'    => admin_url('admin-ajax.php'),
        'auth_nonce'  => wp_create_nonce('gh-auth-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'gymnastics_hub_scripts_styles');

/**
 * Helper: Sort Event Types Taxonomy Terms
 * Order: 1. Tournaments/Competitions, 2. Training Camps, 3. Seminars/Masterclasses
 */
function gh_sort_event_types($terms) {
    if (empty($terms) || is_wp_error($terms)) return $terms;

    $sorted = array();
    $type1 = null; // tournaments
    $type2 = null; // camps
    $type3 = null; // seminars
    $other = array();

    foreach ($terms as $term) {
        $slug = mb_strtolower($term->slug);
        if (strpos($slug, 'turnir') !== false || strpos($slug, 'sorevn') !== false || strpos($slug, 'comp') !== false || strpos($slug, 'tourn') !== false) {
            $type1 = $term;
        } elseif (strpos($slug, 'sbor') !== false || strpos($slug, 'camp') !== false || strpos($slug, 'tren') !== false) {
            $type2 = $term;
        } elseif (strpos($slug, 'semin') !== false || strpos($slug, 'master') !== false || strpos($slug, 'klass') !== false || strpos($slug, 'class') !== false) {
            $type3 = $term;
        } else {
            $other[] = $term;
        }
    }

    if ($type1) $sorted[] = $type1;
    if ($type2) $sorted[] = $type2;
    if ($type3) $sorted[] = $type3;
    
    return array_merge($sorted, $other);
}

/**
 * Helper: Sort Events Array by Type
 * Order: 1. Tournaments/Competitions, 2. Training Camps, 3. Seminars/Masterclasses
 */
function gh_sort_events_by_type($posts) {
    if (empty($posts)) return $posts;

    $type1 = array(); // tournaments
    $type2 = array(); // camps
    $type3 = array(); // seminars
    $other = array(); // other

    foreach ($posts as $post) {
        $terms = get_the_terms($post->ID, 'event_type');
        if (!empty($terms) && !is_wp_error($terms)) {
            $slug = mb_strtolower($terms[0]->slug);
            if (strpos($slug, 'turnir') !== false || strpos($slug, 'sorevn') !== false || strpos($slug, 'comp') !== false || strpos($slug, 'tourn') !== false) {
                $type1[] = $post;
            } elseif (strpos($slug, 'sbor') !== false || strpos($slug, 'camp') !== false || strpos($slug, 'tren') !== false) {
                $type2[] = $post;
            } elseif (strpos($slug, 'semin') !== false || strpos($slug, 'master') !== false || strpos($slug, 'klass') !== false || strpos($slug, 'class') !== false) {
                $type3[] = $post;
            } else {
                $other[] = $post;
            }
        } else {
            $other[] = $post;
        }
    }

    return array_merge($type1, $type2, $type3, $other);
}

/**
 * Russian Pluralization Helper
 * Usage: gh_plural($count, array('ответ', 'ответа', 'ответов'))
 */
function gh_plural($number, $titles) {
    $cases = array(2, 0, 1, 1, 1, 2);
    return $number . ' ' . $titles[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)] ];
}

/**
 * SMTP Mail Configuration
 */
add_action('phpmailer_init', 'gh_smtp_setup');
function gh_smtp_setup($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.mail.ru';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 465; 
    $phpmailer->Username   = 'jauapberme@mail.ru';
    $phpmailer->Password   = 'exiCCExVk8EtjoMFJFzc'; 
    $phpmailer->SMTPSecure = 'ssl'; 
    $phpmailer->From       = 'jauapberme@mail.ru';
    $phpmailer->FromName   = 'Gymnastics Hub';
}

add_filter('wp_mail_from', function($email) { return 'jauapberme@mail.ru'; });
add_filter('wp_mail_from_name', function($name) { return 'Gymnastics Hub'; });

/**
 * Force incomplete coach or club profiles to finish editing
 */
function gh_force_profile_completion() {
    // Avoid loops or blocking background tasks
    if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX) || (defined('DOING_CRON') && DOING_CRON) || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles;

    if (!in_array('gh_club', $user_roles) && !in_array('gh_coach', $user_roles)) {
        return;
    }

    // Check required fields
    $required_fields = array();
    if (in_array('gh_club', $user_roles)) {
        $required_fields = array(
            'gh_country',
            'gh_city',
            'gh_address',
            'description',
            'gh_phone'
        );
    } elseif (in_array('gh_coach', $user_roles)) {
        $required_fields = array(
            'gh_country',
            'gh_city',
            'description',
            'gh_phone'
        );
    }

    $incomplete = false;
    foreach ($required_fields as $field) {
        $value = trim(get_user_meta($current_user->ID, $field, true));
        if (empty($value)) {
            $incomplete = true;
            break;
        }
    }

    if (empty(trim($current_user->display_name))) {
        $incomplete = true;
    }

    if ($incomplete) {
        // If current URL is already profile or verify-email or logout, allow it
        if (is_page('profile') || is_page('verify-email') || strpos($_SERVER['REQUEST_URI'], '/profile/') !== false || strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
            return;
        }
        
        $profile_url = home_url('/profile/');
        wp_redirect(add_query_arg('fill_profile', '1', $profile_url));
        exit;
    }
}
add_action('template_redirect', 'gh_force_profile_completion');
