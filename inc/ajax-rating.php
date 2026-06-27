<?php
/**
 * AJAX Handler for Rating Page
 */

// 1. Voting AJAX Action (Unchanged logic, just keeping it here)
add_action('wp_ajax_rating_vote', 'gh_ajax_rating_vote');
add_action('wp_ajax_nopriv_rating_vote', 'gh_ajax_rating_vote');

function gh_ajax_rating_vote() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Вы должны войти в аккаунт, чтобы проголосовать.'));
    }

    $voter_id   = get_current_user_id();
    $target_id  = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $vote_type  = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : ''; // 'like' or 'dislike'

    if (!$target_id || $vote_type !== 'like') { // We only support like now in the UI
        wp_send_json_error(array('message' => 'Неверные данные.'));
    }

    // Check if targeted user is actually a club or coach
    $target_user = get_userdata($target_id);
    if (!$target_user || !array_intersect(array('gh_club', 'gh_coach'), $target_user->roles)) {
        wp_send_json_error(array('message' => 'Голосовать можно только за клубы и тренеров.'));
    }

    $voted_map = get_user_meta($voter_id, 'gh_voted_users_map', true) ?: array();
    $prev_vote = isset($voted_map[$target_id]) ? $voted_map[$target_id] : null;

    $likes = intval(get_user_meta($target_id, '_gh_likes', true)) ?: 0;

    if ($prev_vote === 'like') {
        $likes = max(0, $likes - 1);
        unset($voted_map[$target_id]);
        $action = 'unvoted';
    } else {
        $likes++;
        $voted_map[$target_id] = 'like';
        $action = 'voted';
    }

    update_user_meta($target_id, '_gh_likes', $likes);
    update_user_meta($target_id, '_gh_rating_score', $likes); // Simplified score
    update_user_meta($voter_id, 'gh_voted_users_map', $voted_map);

    wp_send_json_success(array(
        'likes'      => $likes,
        'action'     => $action,
        'voted_type' => isset($voted_map[$target_id]) ? $voted_map[$target_id] : ''
    ));
}

// 2. Filtering AJAX Action (Updated for Top-3 + Table layout)
add_action('wp_ajax_filter_rating', 'gh_ajax_filter_rating');
add_action('wp_ajax_nopriv_filter_rating', 'gh_ajax_filter_rating');

function gh_ajax_filter_rating() {
    $search = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    $role   = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : 'all';
    $paged  = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $user_args = array(
        'meta_query' => array(
            array(
                'key'     => 'gh_status',
                'value'   => 'approved',
                'compare' => '='
            )
        ),
        'number' => -1,
    );

    if ($role && in_array($role, array('gh_club', 'gh_coach'))) {
        $user_args['role'] = $role;
    } else {
        $user_args['role__in'] = array('gh_club', 'gh_coach');
    }

    if (!empty($search)) {
        $user_args['search'] = '*' . esc_attr($search) . '*';
        $user_args['search_columns'] = array('display_name', 'user_login', 'user_nicename');
    }

    $users = get_users($user_args);

    // Sort by likes descending
    usort($users, function($a, $b) {
        $a_likes = intval(get_user_meta($a->ID, '_gh_likes', true)) ?: 0;
        $b_likes = intval(get_user_meta($b->ID, '_gh_likes', true)) ?: 0;
        
        if ($a_likes === $b_likes) {
            return strcasecmp($a->display_name, $b->display_name);
        }
        return $b_likes - $a_likes;
    });

    $total_users = count($users);
    $per_page = 13; // 3 for top, 10 for table on first page. 
    $total_pages = ceil($total_users / $per_page);
    $paged = max(1, $paged);
    $offset = ($paged - 1) * $per_page;
    $paged_users = array_slice($users, $offset, $per_page);

    $is_logged_in = is_user_logged_in();
    $voted_map = $is_logged_in ? (get_user_meta(get_current_user_id(), 'gh_voted_users_map', true) ?: array()) : array();

    ob_start();

    if (empty($paged_users)) {
        if ($paged == 1) {
            echo '<div class="empty-rating-state" style="text-align: center; padding: 100px 20px; background: #fff; border-radius: 32px; border: 1px solid #f0f0f0; margin-bottom: 40px;">';
            echo '<div class="empty-icon" style="width: 80px; height: 80px; background: #f9f9f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">';
            
            if (!empty($search)) {
                echo '<svg viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="width: 40px; height: 40px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
                echo '</div>';
                echo '<h3 style="font-size: 24px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px;">Ничего не найдено</h3>';
                echo '<p style="color: #666; font-size: 16px; max-width: 400px; margin: 0 auto;">По запросу <b>&laquo;' . esc_html($search) . '&raquo;</b> не найдено ни одного участника. Попробуйте изменить параметры поиска.</p>';
            } else {
                echo '<svg viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="width: 40px; height: 40px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
                echo '</div>';
                echo '<h3 style="font-size: 24px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px;">Пока нет участников</h3>';
                echo '<p style="color: #666; font-size: 16px; max-width: 400px; margin: 0 auto 30px; line-height: 1.5;">В этой категории еще нет профилей. Станьте первым и займите лидирующую позицию в рейтинге!</p>';
                $redirect_url = wp_get_referer() ? wp_get_referer() : home_url('/');
                echo '<a href="' . esc_url(home_url('/login/?redirect_to=' . urlencode($redirect_url) . '#register')) . '" class="btn btn--black" style="border-radius: 100px; padding: 14px 36px; font-weight: 600;">Создать профиль</a>';
            }
            
            echo '</div>';
        }
    } else {
        $top3 = [];
        $table_users = [];

        // If page 1 and no search query (or maybe even with search if they want top 3 of search)
        // Let's do top 3 always on page 1 if there are enough users.
        if ($paged == 1 && count($users) >= 3) {
            $top3 = array_slice($paged_users, 0, 3);
            $table_users = array_slice($paged_users, 3);
        } else {
            $table_users = $paged_users;
        }

        // --- Render Top 3 ---
        if (!empty($top3) && $paged == 1) {
            echo '<h3 class="section-title">Топ-3 Лидера</h3>';
            echo '<div class="top-leaders-grid">';
            
            // Order them for display: 2, 1, 3
            $display_order = [];
            if (isset($top3[1])) $display_order[] = ['rank' => 2, 'user' => $top3[1]];
            if (isset($top3[0])) $display_order[] = ['rank' => 1, 'user' => $top3[0]];
            if (isset($top3[2])) $display_order[] = ['rank' => 3, 'user' => $top3[2]];

            foreach ($display_order as $item) {
                $rank = $item['rank'];
                $u = $item['user'];
                $uid = $u->ID;
                
                $avatar = get_user_meta($uid, 'gh_avatar', true) ?: get_template_directory_uri() . '/img/user_icon.svg';
                $likes = intval(get_user_meta($uid, '_gh_likes', true)) ?: 0;
                if (in_array('gh_club', $u->roles)) {
                    $role_label = 'Клуб';
                } elseif (in_array('gh_coach', $u->roles)) {
                    $role_label = 'Тренер';
                } else {
                    $role_label = 'Организатор';
                }
                $city = get_user_meta($uid, 'gh_city', true) ?: 'Не указан'; // Fallback logic
                if($city == 'Не указан') {
                    // Try to extract location from description or just use role
                    $location_display = $role_label;
                } else {
                    $location_display = $role_label . ' • ' . $city;
                }
                
                $is_voted = isset($voted_map[$uid]) && $voted_map[$uid] === 'like';
                $btn_class = $is_voted ? 'voted' : '';
                $btn_text = $is_voted ? 'Поддержано' : 'Поддержать';

                echo '<div class="top-leader-card rank-'.$rank.'">';
                echo '<div class="leader-rank-badge">#'.$rank.'</div>';
                $author_url = esc_url(get_author_posts_url($uid));
                echo '<a href="'.$author_url.'" style="display: block; width: 100px; height: 100px; margin: 0 auto 15px;"><img src="'.esc_url($avatar).'" alt="'.esc_attr($u->display_name).'" class="leader-avatar" style="margin: 0; width: 100%; height: 100%;"></a>';
                echo '<h4 class="leader-name"><a href="'.$author_url.'" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color=\'#c29661\'" onmouseout="this.style.color=\'inherit\'">'.esc_html($u->display_name).'</a></h4>';
                echo '<div class="leader-meta">'.esc_html($location_display).'</div>';
                echo '<div class="leader-score"><svg width="14" height="14" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg><span class="score-display" data-id="'.$uid.'">'.$likes.'</span></div>';
                echo '<button class="btn-support rate-user-btn '.$btn_class.'" data-id="'.$uid.'">'.$btn_text.'</button>';
                echo '</div>';
            }
            echo '</div>';
        }

        // --- Render Table ---
        if (!empty($table_users)) {
            if ($paged == 1) {
                echo '<h3 class="section-title">Полный рейтинг</h3>';
                echo '<div class="rating-table-wrapper">';
                echo '<table class="rating-table">';
                echo '<thead><tr>
                        <th>Место</th>
                        <th>Участник</th>
                        <!-- <th>Локация</th> -->
                        <th>Рейтинг</th>
                        <th style="text-align: right;">Действие</th>
                      </tr></thead>';
                echo '<tbody>';
            }

            foreach ($table_users as $index => $u) {
                // Calculate global rank
                $global_rank = $offset + ($paged == 1 && count($users) >= 3 ? 3 : 0) + $index + 1;
                if ($paged > 1) {
                    $global_rank = $offset + $index + 1;
                }

                $uid = $u->ID;
                $avatar = get_user_meta($uid, 'gh_avatar', true) ?: get_template_directory_uri() . '/img/user_icon.svg';
                $likes = intval(get_user_meta($uid, '_gh_likes', true)) ?: 0;
                if (in_array('gh_club', $u->roles)) {
                    $role_label = 'Клуб';
                } elseif (in_array('gh_coach', $u->roles)) {
                    $role_label = 'Тренер';
                } else {
                    $role_label = 'Организатор';
                }
                $city = get_user_meta($uid, 'gh_city', true) ?: ''; 
                
                $is_voted = isset($voted_map[$uid]) && $voted_map[$uid] === 'like';
                $btn_class = $is_voted ? 'voted' : '';
                $btn_text = $is_voted ? 'Поддержано' : 'Поддержать';

                echo '<tr>';
                echo '<td class="table-rank">#'.$global_rank.'</td>';
                
                $author_url = esc_url(get_author_posts_url($uid));
                echo '<td>
                        <div class="table-user">
                            <a href="'.$author_url.'" style="flex-shrink: 0;"><img src="'.esc_url($avatar).'" alt="'.esc_attr($u->display_name).'" class="table-avatar"></a>
                            <div>
                                <div class="table-name"><a href="'.$author_url.'" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color=\'#c29661\'" onmouseout="this.style.color=\'inherit\'">'.esc_html($u->display_name).'</a></div>
                                <div class="table-role">'.$role_label.'</div>
                            </div>
                        </div>
                      </td>';
                // echo '<td class="table-location">'.esc_html($city).'</td>';
                echo '<td>
                        <div class="table-score">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#1a1a1a"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            <span class="score-display" data-id="'.$uid.'">'.$likes.'</span>
                        </div>
                      </td>';
                echo '<td style="text-align: right;">
                        <button class="btn-support-outline rate-user-btn '.$btn_class.'" data-id="'.$uid.'">'.$btn_text.'</button>
                      </td>';
                echo '</tr>';
            }

            if ($paged == 1) {
                echo '</tbody></table>';
                if ($total_pages > 1) {
                    echo '<a href="#" class="btn-show-more" data-page="2">Показать еще</a>';
                }
                echo '</div>'; // End rating-table-wrapper
            } else {
                // If appending, send rows and possibly the next "show more" button
                if ($paged < $total_pages) {
                    echo '<a href="#" class="btn-show-more" data-page="'.($paged + 1).'">Показать еще</a>';
                }
            }
        }
    }

    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}
