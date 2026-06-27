<?php
/**
 * AJAX Handler for STEFA AI Assistant
 */

function gh_ai_chat_handler() {
    $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
    $message_low = mb_strtolower($message);
    $home_url = home_url();

    // 0. Personalization
    $user_greeting = "";
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;
        $user_greeting = "Здравствуйте, {$user_name}! 👋 ";
    }

    // 1. Identify Search Intent and Extract Term
    $search_types = array();
    $clean_message = $message_low;
    
    $intents = array(
        'gh_event'      => array('турнир', 'соревнован', 'кубок', 'сбор', 'лагерь', 'camp', 'семинар', 'мастер-класс', 'интенсив'),
        'forum_topic'   => array('форум', 'тема', 'обсуди', 'вопрос', 'спросить', 'мнение'),
        'gh_ad'         => array('объявлен', 'купить', 'продать', 'купальник', 'вещи', 'предметы', 'бу'),
        'gallery_album' => array('фото', 'альбом', 'галерея', 'картинки', 'снимки')
    );

    foreach ($intents as $type => $kws) {
        foreach ($kws as $kw) {
            if (mb_strpos($message_low, $kw) !== false) {
                $search_types[] = $type;
                // Remove the keyword and common suffixes (и, ы, а)
                $clean_message = preg_replace('/' . preg_quote($kw, '/') . '[ыиа]?/iu', '', $clean_message);
            }
        }
    }

    // Clean up noise words and common command prefixes
    $noise = array(' в ', ' на ', ' для ', ' про ', ' с ', ' и ', ' по ', 'найди ', 'покажи ', 'ищи ', 'хочу ', 'найти ');
    foreach ($noise as $n) {
        $clean_message = trim(str_replace($n, ' ', ' ' . $clean_message . ' '));
    }
    
    $search_types = array_unique($search_types);
    $clean_message = trim($clean_message);
    // 2. Perform Intelligent Search
    if (!empty($search_types)) {
        $query_args = array(
            'post_type'      => $search_types,
            'posts_per_page' => 5,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC'
        );

        if (!empty($clean_message) && mb_strlen($clean_message) > 2) {
            $query_args['s'] = $clean_message;
            
            // For events, also try to search by city meta
            if (in_array('gh_event', $search_types)) {
                $query_args['meta_query'] = array(
                    array(
                        'key'     => '_event_location_city',
                        'value'   => $clean_message,
                        'compare' => 'LIKE'
                    )
                );
            }
        }

        $results = new WP_Query($query_args);
        
        // If no results with specific term, try getting latest items
        if (!$results->have_posts() && !empty($query_args['s'])) {
            unset($query_args['s']);
            unset($query_args['meta_query']);
            $query_args['posts_per_page'] = 3;
            $results = new WP_Query($query_args);
            $had_term = true;
        } else {
            $had_term = false;
        }
        
        if ($results->have_posts()) {
            if ($had_term && !empty($clean_message) && mb_strlen($clean_message) > 2) {
                $response = "{$user_greeting}По запросу «{$clean_message}» ничего не нашлось, но вот последние записи: <br><br>";
            } else {
                $response = "{$user_greeting}Конечно! Вот что мне удалось найти: ✨<br><br>";
            }

            while ($results->have_posts()) {
                $results->the_post();
                $type = get_post_type();
                $icon = '🔹';
                $meta = '';
                $thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                
                if ($type === 'gh_event') {
                    $icon = '🏆';
                    $city = get_post_meta(get_the_ID(), '_event_location_city', true);
                    $date = get_post_meta(get_the_ID(), '_event_start_date', true);
                    $meta = ($city ? $city : '') . ($date ? ' | ' . date_i18n('j F', strtotime($date)) : '');
                } elseif ($type === 'forum_topic') {
                    $icon = '💬';
                    $replies = get_comments_number();
                    $meta = gh_plural($replies, array('ответ', 'ответа', 'ответов'));
                } elseif ($type === 'gh_ad') {
                    $icon = '🛍️';
                    $price = get_post_meta(get_the_ID(), '_ad_price', true);
                    $meta = $price ? $price . ' ₸' : 'Цена договорная';
                } elseif ($type === 'gallery_album') {
                    $icon = '📸';
                    $photos = get_post_meta(get_the_ID(), '_gh_album_photos', true);
                    $count = is_array($photos) ? count($photos) : 0;
                    $meta = gh_plural($count, array('фотография', 'фотографии', 'фотографий'));
                }
                
                $response .= "<div class='stefa-result-item' style='display: flex; gap: 12px; margin-bottom: 15px;'>";
                if ($thumb) {
                    $response .= "<img src='{$thumb}' style='width: 50px; height: 50px; border-radius: 8px; object-fit: cover;'>";
                }
                $response .= "<div>";
                $response .= "<a href='".get_permalink()."' style='font-weight: 600; font-size: 14px;'>".get_the_title()."</a><br>";
                if ($meta) $response .= "<small style='opacity: 0.7; font-size: 11px;'>{$icon} {$meta}</small>";
                $response .= "</div></div>";
            }
            
            // Add global link buttons
            $response .= "<div style='margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;'>";
            if (in_array('gh_event', $search_types)) $response .= "<a href='{$home_url}/events/' class='stefa-action-btn'>Весь календарь</a>";
            if (in_array('forum_topic', $search_types)) $response .= "<a href='{$home_url}/forum/' class='stefa-action-btn'>На форум</a>";
            if (in_array('gh_ad', $search_types)) $response .= "<a href='{$home_url}/ads/' class='stefa-action-btn'>Все объявления</a>";
            if (in_array('gallery_album', $search_types)) $response .= "<a href='{$home_url}/gallery/' class='stefa-action-btn'>В галерею</a>";
            $response .= "</div>";
            
            wp_send_json_success($response);
        }
    }

    // 3. Default Keyword Responses
    $kb = array(
        'алина' => "{$user_greeting}Я теперь Стефа! ✨ Но я всё та же верная помощница, просто в новом обличии. Готова помогать вам ещё лучше!",
        'привет' => "{$user_greeting}Привет! 👋 Я Стефа. Я ваш персональный помощник в мире гимнастики. Я могу найти турниры, сборы, помочь с объявлением или подсказать что-то по сайту. О чем хотите узнать?",
        'добавить' => "Конечно! Чтобы <strong>добавить свое мероприятие</strong>, просто перейдите на страницу <a href='{$home_url}/add-event/'>создания события</a>.",
        'реклам' => "По вопросам размещения рекламы вы можете заполнить заявку внизу главной страницы или перейти в раздел <a href='{$home_url}/ads/'>объявлений</a>.",
        'профиль' => "Ваш личный кабинет находится <a href='{$home_url}/profile/'>здесь</a>. Там можно управлять своими событиями и настройками.",
        'фото' => "Вся красота гимнастики собрана в нашей <a href='{$home_url}/gallery/'>медиа-галерее</a>. Заходите посмотреть!",
        'видео' => "Видео с турниров и тренировок можно найти в разделе <a href='{$home_url}/gallery/'>медиа</a>.",
        'форум' => "На нашем <a href='{$home_url}/forum/'>форуме</a> всегда можно найти ответ на любой вопрос или просто пообщаться с единомышленниками!",
        'сайт' => "Gymnastics Hub — это крупнейший портал для гимнастов, тренеров и родителей. Здесь вы найдете календарь турниров, сборов, форум и площадку для объявлений. Я ваша помощница Стефа, всегда готова подсказать!",
        'магазин' => "Наш магазин спортивных товаров скоро откроется! 🛍️ Следите за обновлениями.",
    );

    foreach ($kb as $key => $resp) {
        if (mb_strpos($message_low, $key) !== false) {
            wp_send_json_success($resp);
        }
    }

    // 4. Fallback / General Search
    $general_query = new WP_Query(array(
        'post_type'      => array('gh_event', 'forum_topic', 'gh_ad', 'gallery_album'),
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        's'              => $message
    ));

    if ($general_query->have_posts()) {
        $response = "Я не совсем уверена, что именно вы ищете, но вот что мне удалось найти по вашему запросу: 🤔<br><br>";
        while ($general_query->have_posts()) {
            $general_query->the_post();
            $response .= "🔹 <a href='".get_permalink()."'>".get_the_title()."</a><br><br>";
        }
        $response .= "Если это не то, попробуйте уточнить вопрос (например, 'турниры в Алматы' или 'как продать купальник').";
        wp_send_json_success($response);
    }

    wp_send_json_success("Хм, интересный вопрос! 🤔 Я пока только учусь, но могу помочь вам найти 🏆 <strong>турнир</strong>, ⛺ <strong>сборы</strong> или рассказать, как ➕ <strong>разместить</strong> своё мероприятие. Просто спросите меня!");
}

add_action('wp_ajax_gh_ai_chat', 'gh_ai_chat_handler');
add_action('wp_ajax_nopriv_gh_ai_chat', 'gh_ai_chat_handler');
