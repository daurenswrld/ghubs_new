<?php 
/**
 * The template for displaying Forum Topic archives
 */
get_header(); 
?>

    <main class="main-content forum-page">
        <section class="forum-section">
            <div class="container container--wide">
                <div class="gallery-header">
                    <div class="gallery-header__left">
                        <h1 class="page-title" style="color: #000000;">Форум</h1>
                        <p class="page-subtitle">Обсуждения, вопросы и ответы сообщества Gymnastics Hub</p>
                    </div>
                    <div class="gallery-header__right">
                        <a href="<?php echo esc_url(home_url('/add-topic/')); ?>" class="btn btn--black btn--pill">
                            <span class="plus-icon">+</span> Начать обсуждение
                        </a>
                    </div>
                </div>

                <div class="gallery-search-row">
                    <div class="search-input-wrapper" style="padding: 10px 20px;">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="forumSearch" placeholder="Поиск по теме">
                    </div>
                    <div class="sort-select-wrapper">
                        <select id="forumSort">
                            <option value="newest">Сначала новые</option>
                            <option value="popular">Сначала популярные</option>
                            <option value="oldest">Сначала старые</option>
                        </select>
                        <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>

                <div class="forum-posts-list" id="forumResults">
                    <?php
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $args = array(
                        'post_type'      => 'forum_topic',
                        'posts_per_page' => 10,
                        'paged'          => $paged,
                        'orderby'        => 'date',
                        'order'          => 'DESC'
                    );

                    $forum_query = new WP_Query($args);

                    if ($forum_query->have_posts()) :
                        while ($forum_query->have_posts()) : $forum_query->the_post();
                            $author_id = get_the_author_meta('ID');
                            $avatar_url = get_user_meta($author_id, 'gh_avatar', true);
                            if (!$avatar_url) {
                                $avatar_url = get_template_directory_uri() . '/img/user-placeholder.png';
                            }
                            
                            $likes = get_post_meta(get_the_ID(), '_gh_likes', true) ?: 0;
                            $dislikes = get_post_meta(get_the_ID(), '_gh_dislikes', true) ?: 0;
                            $replies_count = get_comments_number();

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
                                            <span class="count"><?php echo esc_html($likes); ?></span>
                                        </button>
                                        <button class="action-item vote-btn <?php echo ($voted_type === 'dislike') ? 'voted' : ''; ?>" data-id="<?php the_ID(); ?>" data-type="dislike">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                            <span class="count"><?php echo esc_html($dislikes); ?></span>
                                        </button>
                                    </div>
                                    <a href="<?php the_permalink(); ?>#comments" class="btn-reply">Ответить</a>
                                    <div class="comments-count">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        <?php echo gh_plural($replies_count, array('ответ', 'ответа', 'ответов')); ?>
                                    </div>
                                </div>
                            </div>
                    <?php
                        endwhile;
                        
                        // Pagination
                        echo '<div class="pagination">';
                        echo paginate_links(array(
                            'total' => $forum_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '‹',
                            'next_text' => '›',
                        ));
                        echo '</div>';
                        
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="empty-forum-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </div>
                            <h3>Обсуждений пока нет</h3>
                            <p>Станьте первым, кто поднимет интересную тему в сообществе Gymnastics Hub!</p>
                            <a href="<?php echo esc_url(home_url('/add-topic/')); ?>" class="btn btn--black btn--pill">Начать обсуждение</a>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
            </div>
        </section>
    </main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const forumResults = document.getElementById('forumResults');
    const searchInput  = document.getElementById('forumSearch');
    const sortSelect   = document.getElementById('forumSort');

    async function updateForum() {
        forumResults.style.opacity = '0.5';
        
        const formData = new FormData();
        formData.append('action', 'filter_forum');
        formData.append('s', searchInput.value);
        formData.append('sort', sortSelect.value);

        try {
            const response = await fetch(themeData.ajax_url, {
                method: 'POST',
                body: formData
            });
            const res = await response.json();
            if (res.success) {
                forumResults.innerHTML = res.data;
            }
        } catch (err) {
            console.error(err);
        } finally {
            forumResults.style.opacity = '1';
        }
    }

    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(updateForum, 500);
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', updateForum);
    }
});
</script>

<style>
.empty-forum-state {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 40px;
    border: 1px solid #f0f0f0;
}
.empty-forum-state .empty-icon {
    width: 80px;
    height: 80px;
    background: #f9f9f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: #ccc;
}
.empty-forum-state .empty-icon svg {
    width: 40px;
    height: 40px;
}
.empty-forum-state h3 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 12px;
}
.empty-forum-state p {
    color: #666;
    font-size: 16px;
    max-width: 400px;
    margin: 0 auto 30px;
    line-height: 1.6;
}
</style>

<?php get_footer(); ?>
