<?php 
/**
 * The template for displaying single forum topics
 */
get_header(); 

while (have_posts()) : the_post();
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

    <main class="main-content forum-single-page">
        <div class="container container--wide">

            <!-- ===================== FEATURED POST ===================== -->
            <div class="forum-post-card forum-post-card--top">
                <div class="forum-post-card__header">
                    <div class="user-avatar">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr(get_the_author()); ?>">
                    </div>
                    <span class="user-name"><?php the_author(); ?></span>
                    <span class="post-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' назад'; ?></span>
                </div>
                <h1 class="forum-post-card__title"><?php the_title(); ?></h1>
                <div class="forum-post-card__body content-area">
                    <?php the_content(); ?>
                </div>
                <div class="forum-post-card__footer">
                    <div class="action-group">
                        <button class="action-item vote-btn <?php echo ($voted_type === 'like') ? 'voted' : ''; ?>" data-id="<?php the_ID(); ?>" data-type="like">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                            <span class="count"><?php echo esc_html($likes); ?></span>
                        </button>
                        <button class="action-item vote-btn <?php echo ($voted_type === 'dislike') ? 'voted' : ''; ?>" data-id="<?php the_ID(); ?>" data-type="dislike">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                            <span class="count"><?php echo esc_html($dislikes); ?></span>
                        </button>
                    </div>
                    <button class="btn-reply" id="replyMainBtn">Ответить</button>
                    <div class="comments-count">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <?php 
                        $replies_count = get_comments_number();
                        echo gh_plural($replies_count, array('ответ', 'ответа', 'ответов')); 
                        ?>
                    </div>
                </div>
            </div>

            <!-- ===================== COMMENTS ===================== -->
            <div class="forum-comments">
                <?php 
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif; 
                ?>
            </div>

        </div>
    </main>

<?php 
endwhile;
get_footer(); 
?>
