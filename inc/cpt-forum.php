<?php
/**
 * Register Forum Custom Post Type
 */
function gh_register_forum_cpt() {
    $labels = array(
        'name'                  => 'Форум',
        'singular_name'         => 'Тема форума',
        'menu_name'             => 'Форум',
        'name_admin_bar'        => 'Тему форума',
        'archives'              => 'Архив тем',
        'attributes'            => 'Атрибуты темы',
        'parent_item_colon'     => 'Родительская тема:',
        'all_items'             => 'Все темы',
        'add_new_item'          => 'Добавить новую тему',
        'add_new'               => 'Добавить новую',
        'new_item'              => 'Новая тема',
        'edit_item'             => 'Редактировать тему',
        'update_item'           => 'Обновить тему',
        'view_item'             => 'Просмотреть тему',
        'view_items'            => 'Просмотреть темы',
        'search_items'          => 'Искать темы',
        'not_found'             => 'Темы не найдены',
        'not_found_in_trash'    => 'В корзине тем не найдено',
        'featured_image'        => 'Изображение темы',
        'set_featured_image'    => 'Установить изображение темы',
        'remove_featured_image' => 'Удалить изображение темы',
        'use_featured_image'    => 'Использовать как изображение темы',
        'insert_into_item'      => 'Вставить в тему',
        'uploaded_to_this_item' => 'Загружено для этой темы',
        'items_list'            => 'Список тем',
        'items_list_navigation' => 'Навигация по списку тем',
        'filter_items_list'     => 'Фильтровать список тем',
    );
    $args = array(
        'label'                 => 'Тема форума',
        'description'           => 'Темы обсуждений на форуме',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'author', 'thumbnail', 'comments', 'excerpt', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'forum',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'capabilities' => array(
            'create_posts' => 'do_not_allow', // Disable "Add New" in admin
        ),
        'map_meta_cap' => true,
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'forum'),
    );
    register_post_type('forum_topic', $args);
}
add_action('init', 'gh_register_forum_cpt');

/**
 * Log activity when forum topic is approved
 */
function gh_log_forum_activity_on_approval($new_status, $old_status, $post) {
    if ($post->post_type !== 'forum_topic') return;
    
    // When status changes to publish (approved)
    if ($new_status === 'publish' && $old_status !== 'publish') {
        $user_id = $post->post_author;
        $activities = get_user_meta($user_id, 'gh_recent_activity', true) ?: array();
        
        // Add new activity
        $new_activity = array(
            'type' => 'forum_topic_approved',
            'title' => 'Тема одобрена: ' . $post->post_title,
            'link' => get_permalink($post->ID),
            'time' => current_time('mysql')
        );
        
        array_unshift($activities, $new_activity);
        $activities = array_slice($activities, 0, 10); // Keep last 10
        
        update_user_meta($user_id, 'gh_recent_activity', $activities);
    }
}
add_action('transition_post_status', 'gh_log_forum_activity_on_approval', 10, 3);

/**
 * Custom Comment Callback for Forum
 */
function gh_forum_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $author_id = $comment->user_id;
    $avatar_url = get_user_meta($author_id, 'gh_avatar', true);
    if (!$avatar_url) {
        $avatar_url = get_template_directory_uri() . '/img/user-placeholder.png';
    }
    ?>
    <div id="comment-<?php comment_ID(); ?>" <?php comment_class($depth > 1 ? 'comment-reply' : 'comment-node'); ?>>
        <div class="forum-post-card">
            <div class="forum-post-card__header">
                <div class="user-avatar">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr(get_comment_author()); ?>">
                </div>
                <span class="user-name"><?php comment_author(); ?></span>
                <span class="post-time"><?php printf(__('%1$s назад'), human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?></span>
            </div>
            <div class="forum-post-card__excerpt">
                <?php comment_text(); ?>
            </div>
            <div class="forum-post-card__footer">
                <div class="action-group">
                    <?php
                    $likes = get_comment_meta(get_comment_ID(), '_gh_comment_likes', true) ?: 0;
                    $dislikes = get_comment_meta(get_comment_ID(), '_gh_comment_dislikes', true) ?: 0;

                    // Check if current user already voted for this comment
                    $voted_type = '';
                    if (is_user_logged_in()) {
                        $voted_map = get_user_meta(get_current_user_id(), 'gh_voted_comments_map', true) ?: array();
                        $voted_type = isset($voted_map[get_comment_ID()]) ? $voted_map[get_comment_ID()] : '';
                    }
                    ?>
                    <?php
                    $is_deleted = get_comment_meta(get_comment_ID(), '_gh_is_deleted', true);
                    if (!$is_deleted) : ?>
                        <button class="action-item vote-comment-btn <?php echo ($voted_type === 'like') ? 'voted' : ''; ?>" data-id="<?php comment_ID(); ?>" data-type="like">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                            <span class="count"><?php echo $likes; ?></span>
                        </button>
                        <button class="action-item vote-comment-btn <?php echo ($voted_type === 'dislike') ? 'voted' : ''; ?>" data-id="<?php comment_ID(); ?>" data-type="dislike">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                            <span class="count"><?php echo $dislikes; ?></span>
                        </button>
                    <?php endif; ?>
                </div>
                <?php 
                if (!$is_deleted) {
                    comment_reply_link(array_merge($args, array(
                        'add_below' => 'comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'reply_text' => 'Ответить',
                        'login_text' => 'Войдите, чтобы ответить',
                        'before'    => '',
                        'after'     => ''
                    ))); 

                    if (is_user_logged_in() && get_current_user_id() == $author_id) : ?>
                        <button class="action-item delete-comment-btn" data-id="<?php comment_ID(); ?>">
                            Удалить
                        </button>
                    <?php endif;
                }
                ?>
            </div>
        </div>
    <?php
}

/**
 * AJAX Vote Handler
 */
function gh_ajax_forum_vote() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Need login');
    }

    $user_id   = get_current_user_id();
    $item_id   = intval($_POST['id']);
    $new_type  = sanitize_text_field($_POST['type']); // 'like' or 'dislike'
    $vote_for  = sanitize_text_field($_POST['vote_for']); // 'topic' or 'comment'

    if (!$item_id) wp_send_json_error('Invalid ID');

    $user_voted_meta = ($vote_for == 'topic') ? 'gh_voted_topics_map' : 'gh_voted_comments_map';
    $voted_map = get_user_meta($user_id, $user_voted_meta, true) ?: array();

    $prev_type = isset($voted_map[$item_id]) ? $voted_map[$item_id] : null;

    // Helper to get meta key
    $get_key = function($type, $for) {
        return ($for == 'topic') ? "_gh_{$type}s" : "_gh_comment_{$type}s";
    };

    // If already voted same type, we "unvote"
    if ($prev_type === $new_type) {
        $meta_key = $get_key($new_type, $vote_for);
        if ($vote_for == 'topic') {
            $count = get_post_meta($item_id, $meta_key, true) ?: 0;
            update_post_meta($item_id, $meta_key, max(0, $count - 1));
        } else {
            $count = get_comment_meta($item_id, $meta_key, true) ?: 0;
            update_comment_meta($item_id, $meta_key, max(0, $count - 1));
        }
        
        unset($voted_map[$item_id]);
        update_user_meta($user_id, $user_voted_meta, $voted_map);
        
        $res_likes = ($vote_for == 'topic') ? get_post_meta($item_id, '_gh_likes', true) : get_comment_meta($item_id, '_gh_comment_likes', true);
        $res_dislikes = ($vote_for == 'topic') ? get_post_meta($item_id, '_gh_dislikes', true) : get_comment_meta($item_id, '_gh_comment_dislikes', true);

        wp_send_json_success(array(
            'likes' => $res_likes ?: 0, 
            'dislikes' => $res_dislikes ?: 0, 
            'action' => 'unvoted'
        ));
    }

    // If switching (e.g. like -> dislike)
    if ($prev_type && $prev_type !== $new_type) {
        $old_meta_key = $get_key($prev_type, $vote_for);
        if ($vote_for == 'topic') {
            $old_count = get_post_meta($item_id, $old_meta_key, true) ?: 0;
            update_post_meta($item_id, $old_meta_key, max(0, $old_count - 1));
        } else {
            $old_count = get_comment_meta($item_id, $old_meta_key, true) ?: 0;
            update_comment_meta($item_id, $old_meta_key, max(0, $old_count - 1));
        }
    }

    // Increment new
    $new_meta_key = $get_key($new_type, $vote_for);
    if ($vote_for == 'topic') {
        $new_count = get_post_meta($item_id, $new_meta_key, true) ?: 0;
        update_post_meta($item_id, $new_meta_key, $new_count + 1);
    } else {
        $new_count = get_comment_meta($item_id, $new_meta_key, true) ?: 0;
        update_comment_meta($item_id, $new_meta_key, $new_count + 1);
    }

    // Save to user history
    $voted_map[$item_id] = $new_type;
    update_user_meta($user_id, $user_voted_meta, $voted_map);

    // Return both counts for UI sync
    $res_likes = ($vote_for == 'topic') ? get_post_meta($item_id, '_gh_likes', true) : get_comment_meta($item_id, '_gh_comment_likes', true);
    $res_dislikes = ($vote_for == 'topic') ? get_post_meta($item_id, '_gh_dislikes', true) : get_comment_meta($item_id, '_gh_comment_dislikes', true);

    wp_send_json_success(array(
        'likes' => $res_likes ?: 0,
        'dislikes' => $res_dislikes ?: 0,
        'action' => 'voted'
    ));
}
add_action('wp_ajax_forum_vote', 'gh_ajax_forum_vote');
add_action('wp_ajax_nopriv_forum_vote', 'gh_ajax_forum_vote');

/**
 * Handle new topic submission
 */
function gh_add_forum_topic() {
    if (!is_user_logged_in()) wp_send_json_error('You must be logged in');

    $title   = sanitize_text_field($_POST['title']);
    $content = wp_kses_post($_POST['content']);

    if (empty($title) || empty($content)) wp_send_json_error('Please fill all fields');

    $post_id = wp_insert_post(array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'pending',
        'post_type'    => 'forum_topic',
        'post_author'  => get_current_user_id()
    ));

    if (is_wp_error($post_id)) wp_send_json_error('Failed to create topic');

    wp_send_json_success(array(
        'message'  => 'Тема успешно создана!',
        'redirect' => get_permalink($post_id)
    ));
}
add_action('wp_ajax_gh_add_forum_topic', 'gh_add_forum_topic');

/**
 * Handle Forum Filtering AJAX
 */
function gh_ajax_filter_forum() {
    $search = sanitize_text_field($_POST['s']);
    $sort   = sanitize_text_field($_POST['sort']);
    $paged  = intval($_POST['paged']) ?: 1;

    $args = array(
        'post_type'      => 'forum_topic',
        'posts_per_page' => 10,
        'paged'          => $paged,
        's'              => $search
    );

    if ($sort == 'popular') {
        $args['meta_key'] = '_gh_likes';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'DESC';
    } elseif ($sort == 'oldest') {
        $args['orderby']  = 'date';
        $args['order']    = 'ASC';
    } else {
        $args['orderby']  = 'date';
        $args['order']    = 'DESC';
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $author_id = get_the_author_meta('ID');
            $avatar_url = get_user_meta($author_id, 'gh_avatar', true);
            if (!$avatar_url) {
                $avatar_url = get_template_directory_uri() . '/img/user-placeholder.png';
            }
            
            $likes = get_post_meta(get_the_ID(), '_gh_likes', true) ?: 0;
            $dislikes = get_post_meta(get_the_ID(), '_gh_dislikes', true) ?: 0;

            // Check if current user already voted
            $voted_type = '';
            if (is_user_logged_in()) {
                $voted_map = get_user_meta(get_current_user_id(), 'gh_voted_topics_map', true) ?: array();
                $voted_type = isset($voted_map[get_the_ID()]) ? $voted_map[get_the_ID()] : '';
            }
            ?>
            <div class="forum-post-card">
                <div class="forum-post-card__header">
                    <div class="user-avatar">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr(get_the_author()); ?>">
                    </div>
                    <span class="user-name"><?php the_author(); ?></span>
                    <span class="post-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' назад'; ?></span>
                </div>
                <a href="<?php the_permalink(); ?>" class="forum-post-card__title"><?php the_title(); ?></a>
                <div class="forum-post-card__excerpt">
                    <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                </div>
                <div class="forum-post-card__footer">
                    <div class="action-group">
                        <button class="action-item vote-btn <?php echo ($voted_type === 'like') ? 'voted' : ''; ?>" data-id="<?php the_ID(); ?>" data-type="like">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                            <span class="count"><?php echo $likes; ?></span>
                        </button>
                        <button class="action-item vote-btn <?php echo ($voted_type === 'dislike') ? 'voted' : ''; ?>" data-id="<?php the_ID(); ?>" data-type="dislike">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                            <span class="count"><?php echo $dislikes; ?></span>
                        </button>
                    </div>
                    <a href="<?php the_permalink(); ?>#comments" class="btn-reply">Ответить</a>
                    <div class="comments-count">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <?php 
                        $replies_count = get_comments_number();
                        echo gh_plural($replies_count, array('ответ', 'ответа', 'ответов')); 
                        ?>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<div class="empty-state">По вашему запросу ничего не найдено.</div>';
    endif;

    $data = ob_get_clean();
    wp_send_json_success($data);
}
add_action('wp_ajax_filter_forum', 'gh_ajax_filter_forum');
add_action('wp_ajax_nopriv_filter_forum', 'gh_ajax_filter_forum');

/**
 * AJAX Submit Comment
 */
add_action('wp_ajax_submit_comment', 'gh_ajax_submit_comment');
add_action('wp_ajax_nopriv_submit_comment', 'gh_ajax_submit_comment');

function gh_ajax_submit_comment() {
    $comment_post_id = isset($_POST['comment_post_ID']) ? intval($_POST['comment_post_ID']) : 0;
    $comment_content = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $comment_parent  = isset($_POST['comment_parent']) ? intval($_POST['comment_parent']) : 0;

    if (!$comment_post_id || empty($comment_content)) {
        wp_send_json_error(array('message' => 'Пожалуйста, напишите текст ответа.'));
    }

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Вы должны быть авторизованы для ответа.'));
    }

    $user = wp_get_current_user();
    $comment_data = array(
        'comment_post_ID' => $comment_post_id,
        'comment_author'  => $user->display_name,
        'comment_author_email' => $user->user_email,
        'comment_content' => $comment_content,
        'comment_type'    => 'comment',
        'comment_parent'  => $comment_parent,
        'user_id'         => $user->ID,
        'comment_approved' => 1, // Automatically approve for logged in users on forum
    );

    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        $comment = get_comment($comment_id);
        
        // Output the new comment using our callback
        ob_start();
        gh_forum_comment_callback($comment, array('max_depth' => 3), 1);
        $comment_html = ob_get_clean();

        wp_send_json_success(array(
            'message' => 'Ответ добавлен!',
            'html'    => $comment_html,
            'parent'  => $comment_parent
        ));
    } else {
        wp_send_json_error(array('message' => 'Ошибка при сохранении ответа.'));
    }
    die();
}

/**
 * AJAX Delete Comment
 */
add_action('wp_ajax_delete_forum_comment', 'gh_ajax_delete_forum_comment');

function gh_ajax_delete_forum_comment() {
    $comment_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (!$comment_id) {
        wp_send_json_error(array('message' => 'ID комментария не указан.'));
    }

    $comment = get_comment($comment_id);
    if (!$comment) {
        wp_send_json_error(array('message' => 'Комментарий не найден.'));
    }

    if (!is_user_logged_in() || get_current_user_id() != $comment->user_id) {
        wp_send_json_error(array('message' => 'У вас нет прав для удаления этого ответа.'));
    }

    // "Soft delete" - replace content and set meta flag
    $update_data = array(
        'comment_ID'      => $comment_id,
        'comment_content' => '<i>Этот ответ был удален автором.</i>'
    );

    wp_update_comment($update_data);
    update_comment_meta($comment_id, '_gh_is_deleted', 1);

    wp_send_json_success(array('message' => 'Ответ удален.'));
}

