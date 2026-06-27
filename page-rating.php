<?php
/**
 * Template Name: Рейтинг
 */
get_header(); 
?>

<main class="main-content rating-page">
    <section class="rating-hero" style="background-color: #fafafa; padding: 60px 0 40px; text-align: center;">
        <div class="container">
            <h1 style="font-size: 42px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px;">Рейтинг клубов и тренеров</h1>
            <p style="font-size: 16px; color: #666; max-width: 750px; margin: 0 auto 30px; line-height: 1.6;">
                Рейтинг GymnasticsHub создан для поддержки и развития гимнастического сообщества.<br>
                Он помогает спортсменам находить лучших наставников и сильные клубы,<br>
                а тренерам и организациям — получать заслуженное признание и привлекать новые таланты.
            </p>
            <div class="rating-hero-actions">
                <a href="<?php echo esc_url(home_url('/profile/')); ?>" class="btn btn--black" style="border-radius: 100px; padding: 12px 30px;">Найти профиль</a>
                <a href="<?php echo esc_url(home_url('/login/?redirect_to=' . urlencode(get_permalink()) . '#register')); ?>" class="btn btn--outline-dark" style="border-radius: 100px; padding: 12px 30px; border-color: #ccc;">Присоединиться</a>
            </div>
        </div>
    </section>

    <section class="rating-content" style="padding: 40px 0 30px; background-color: #fafafa;">
        <div class="container container--wide">
            <!-- Filters -->
            <div class="rating-filters-bar">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="ratingSearch" placeholder="Поиск по названию, имени, локации">
                </div>

                <div class="rating-tabs">
                    <button class="rating-tab-btn active" data-role="all">Все</button>
                    <button class="rating-tab-btn" data-role="gh_club">Клубы</button>
                    <button class="rating-tab-btn" data-role="gh_coach">Тренеры</button>
                </div>
            </div>

            <!-- AJAX Results -->
            <div id="ratingResults" class="rating-results-wrapper">
                <div class="rating-loading"><div class="spinner"></div></div>
            </div>
        </div>
    </section>

    <!-- Info Sections -->
    <section class="rating-info-section" style="padding: 40px 0 80px; background-color: #fafafa;">
        <div class="container container--wide">
            <div class="rating-info-grid">
                <div class="rating-info-card" style="background: #f5f5f5; border-radius: 24px; padding: 40px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px;">Зачем нужен рейтинг?</h2>
                    <p style="font-size: 15px; color: #666; line-height: 1.6; margin-bottom: 30px;">
                        Рейтинг GymnasticsHub создан для поддержки и развития гимнастического сообщества. Он помогает спортсменам находить лучших наставников и сильные клубы, а тренерам и организациям — получать заслуженное признание и привлекать новые таланты.
                    </p>
                </div>

                <div class="rating-info-card" style="background: #f5f5f5; border-radius: 24px; padding: 40px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #1a1a1a; margin-bottom: 30px; text-align: center;">Как это работает</h2>
                    <div class="how-it-works-grid">
                        <div style="text-align: center;">
                            <div style="width: 50px; height: 50px; background: #e0e0e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">1. Зарегистрируйтесь</h4>
                            <p style="font-size: 13px; color: #666; line-height: 1.4;">Создайте профиль тренера или клуба на платформе.</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="width: 50px; height: 50px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">2. Получайте лайки</h4>
                            <p style="font-size: 13px; color: #666; line-height: 1.4;">Сообщество оценивает вашу работу и достижения.</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="width: 50px; height: 50px; background: #1a1a1a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">3. Поднимайтесь в топ</h4>
                            <p style="font-size: 13px; color: #666; line-height: 1.4;">Становитесь лидером рейтинга и привлекайте внимание.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Login Required Modal -->
<div class="global-modal-overlay" id="loginPromptModal" style="justify-content: center; " data-lenis-prevent>
    <div class="global-modal global-modal--form" style="max-width: 500px; text-align: center; padding: 40px 30px;">
        <button class="modal-close" id="closeLoginPrompt" aria-label="Закрыть">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <h2 class="modal-title" style="margin-bottom: 12px; font-size: 22px; font-weight: 700; color: #1a1a1a;">Требуется авторизация</h2>
        <p style="font-size: 14px; color: #666; margin-bottom: 24px; line-height: 1.5;">
            Чтобы оценивать клубы и тренеров, пожалуйста, войдите в свой аккаунт или зарегистрируйтесь.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; width: 100%;">
            <a href="<?php echo esc_url(home_url('/login/?redirect_to=' . urlencode(get_permalink()))); ?>" class="btn btn--black btn--pill" style="flex: 1; text-align: center; text-decoration: none; padding: 12px 0; border-radius: 100px;">Войти</a>
            <a href="<?php echo esc_url(home_url('/login/?redirect_to=' . urlencode(get_permalink()) . '#register')); ?>" class="btn btn--outline-dark btn--pill" style="flex: 1; text-align: center; text-decoration: none; padding: 12px 0; border-radius: 100px; border-color: #ccc;">Регистрация</a>
        </div>
    </div>
</div>

<style>
/* --- Premium Styles for Rating Page --- */
.rating-page { background: #fafafa; }

.rating-filters-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 40px;
}
.search-input-wrapper {
    position: relative;
    max-width: 450px;
    width: 100%;
}
.search-input-wrapper input {
    width: 100%;
    padding: 14px 20px 14px 50px;
    border: none;
    border-radius: 100px;
    background: #f0f0f0;
    font-size: 15px;
    outline: none;
    font-family: inherit;
    transition: all 0.3s;
}
.search-input-wrapper input:focus {
    background: #e8e8e8;
}
.search-input-wrapper .search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}
.rating-tabs {
    display: flex;
    gap: 12px;
}
.rating-tab-btn {
    border: 1px solid #1a1a1a;
    background: transparent;
    padding: 10px 28px;
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    border-radius: 100px;
    cursor: pointer;
    transition: all 0.3s;
}
.rating-tab-btn.active, .rating-tab-btn:hover {
    background: #1a1a1a;
    color: #fff;
}

/* Info Grid */
.rating-info-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

/* Top 3 Section */
.section-title {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 30px;
}
.top-leaders-grid {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 60px;
}
.top-leader-card {
    background: #fff;
    border-radius: 24px;
    padding: 30px;
    text-align: center;
    position: relative;
    border: 1px solid #f0f0f0;
    width: 30%;
    max-width: 320px;
    transition: transform 0.3s;
}
.top-leader-card:hover { transform: translateY(-5px); }

.top-leader-card.rank-1 {
    width: 38%;
    max-width: 380px;
    border: 2px solid #1a1a1a;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    padding: 40px 30px;
    z-index: 2;
}
.top-leader-card.rank-2 { order: 1; }
.top-leader-card.rank-1 { order: 2; }
.top-leader-card.rank-3 { order: 3; }

.leader-rank-badge {
    position: absolute;
    top: -12px;
    left: 20px;
    background: #f5f5f5;
    color: #666;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 100px;
    border: 1px solid #e0e0e0;
}
.top-leader-card.rank-1 .leader-rank-badge {
    background: #1a1a1a;
    color: #fff;
    border: none;
    font-size: 18px;
    padding: 8px 16px;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
}
.leader-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 20px;
    object-fit: cover;
    border: 3px solid #f9f9f9;
}
.top-leader-card.rank-1 .leader-avatar {
    width: 140px;
    height: 140px;
}
.leader-name {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.leader-meta {
    font-size: 13px;
    color: #888;
    margin-bottom: 16px;
}
.leader-score {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f5f5f5;
    padding: 6px 16px;
    border-radius: 100px;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 24px;
    color: #1a1a1a;
}
.leader-score svg {
    fill: #1a1a1a;
}
.btn-support {
    width: 100%;
    background: #1a1a1a;
    color: #fff;
    border: none;
    padding: 14px;
    border-radius: 100px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    font-size: 14px;
}
.btn-support:hover { background: #333; }
.btn-support.voted {
    background: #f5f5f5;
    color: #1a1a1a;
    border: 1px solid #e0e0e0;
}

/* Table Section */
.rating-table-wrapper {
    background: #fff;
    border-radius: 24px;
    border: 1px solid #f0f0f0;
    overflow: hidden;
    margin-bottom: 40px;
}
.rating-table {
    width: 100%;
    border-collapse: collapse;
}
.rating-table th {
    text-align: left;
    padding: 20px 24px;
    font-size: 13px;
    color: #888;
    font-weight: 600;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
}
.rating-table td {
    padding: 16px 24px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}
.rating-table tr:last-child td { border-bottom: none; }
.rating-table tr:hover td { background: #fafafa; }
.table-rank {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}
.table-user {
    display: flex;
    align-items: center;
    gap: 16px;
}
.table-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #f0f0f0;
}
.table-name {
    font-weight: 700;
    color: #1a1a1a;
    font-size: 15px;
    margin-bottom: 2px;
}
.table-role {
    font-size: 12px;
    color: #888;
}
.table-location {
    font-size: 14px;
    color: #666;
}
.table-score {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    font-size: 14px;
    color: #1a1a1a;
}
.btn-support-outline {
    background: transparent;
    border: 1px solid #1a1a1a;
    color: #1a1a1a;
    padding: 10px 24px;
    border-radius: 100px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.btn-support-outline:hover {
    background: #f5f5f5;
}
.btn-support-outline.voted {
    background: #f5f5f5;
    border-color: #ddd;
    color: #888;
}
.btn-show-more {
    display: block;
    width: 100%;
    text-align: center;
    padding: 20px;
    background: #fff;
    border-top: 1px solid #f0f0f0;
    color: #1a1a1a;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
}
.btn-show-more:hover { background: #fafafa; }

/* Loading & empty */
.rating-loading {
    display: flex;
    justify-content: center;
    padding: 80px 0;
}
.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    border-top-color: #000;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.empty-rating-state {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 24px;
    border: 1px solid #f0f0f0;
}
.empty-rating-state h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.empty-rating-state p { color: #666; font-size: 15px; }

.how-it-works-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.rating-hero-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Responsive fixes */
@media (max-width: 1024px) {
    .rating-filters-bar { flex-direction: column; align-items: stretch; }
    .search-input-wrapper { max-width: 100%; }
    .rating-tabs { justify-content: center; flex-wrap: wrap; }
    .rating-info-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .top-leaders-grid { flex-direction: column; align-items: center; }
    .top-leader-card { width: 100%; max-width: 320px; order: 0 !important; }
    .top-leader-card.rank-1 { width: 100%; max-width: 320px; padding: 30px; }
    .rating-table-wrapper { overflow-x: auto; }
    .rating-table { min-width: 600px; }
    .how-it-works-grid { grid-template-columns: 1fr; gap: 30px; }
    .rating-info-card { padding: 30px 20px !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const resultsContainer = document.getElementById('ratingResults');
    const searchInput = document.getElementById('ratingSearch');
    const tabButtons = document.querySelectorAll('.rating-tab-btn');
    const loginModal = document.getElementById('loginPromptModal');
    const closeLoginBtn = document.getElementById('closeLoginPrompt');

    let currentRole = 'all';

    // 1. Filter and search list function
    async function updateRatingList(page = 1, append = false) {
        if (!resultsContainer) return;
        
        if (!append) {
            resultsContainer.style.opacity = '0.5';
        }

        const formData = new FormData();
        formData.append('action', 'filter_rating');
        formData.append('s', searchInput ? searchInput.value : '');
        formData.append('role', currentRole);
        formData.append('sort', 'rating'); // Always sort by rating now
        formData.append('paged', page);

        try {
            const response = await fetch(themeData.ajax_url, {
                method: 'POST',
                body: formData
            });
            const res = await response.json();
            
            if (res.success) {
                if (append) {
                    // Remove the old show more button if exists
                    const oldBtn = resultsContainer.querySelector('.btn-show-more');
                    if (oldBtn) oldBtn.remove();
                    
                    // Append new rows to table body
                    const temp = document.createElement('div');
                    temp.innerHTML = (res.data && res.data.html) ? res.data.html : (res.data || '');
                    
                    const newRows = temp.querySelectorAll('tbody tr');
                    const tbody = resultsContainer.querySelector('tbody');
                    if (tbody && newRows.length > 0) {
                        newRows.forEach(row => tbody.appendChild(row));
                    }
                    
                    // Append new show more button if there is one
                    const newBtn = temp.querySelector('.btn-show-more');
                    if (newBtn) {
                        resultsContainer.querySelector('.rating-table-wrapper').appendChild(newBtn);
                    }
                } else {
                    resultsContainer.innerHTML = (res.data && res.data.html) ? res.data.html : (res.data || '');
                }
                
                setupPaginationListeners();
            }
        } catch (err) {
            console.error('Error fetching rating list:', err);
        } finally {
            resultsContainer.style.opacity = '1';
        }
    }

    // Handle show more click
    function setupPaginationListeners() {
        const showMoreBtn = resultsContainer.querySelector('.btn-show-more');
        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const page = showMoreBtn.getAttribute('data-page');
                if (page) {
                    updateRatingList(parseInt(page), true);
                    showMoreBtn.textContent = 'Загрузка...';
                    showMoreBtn.style.pointerEvents = 'none';
                }
            });
        }
    }

    // 2. Set event listeners for search and filter controls
    if (searchInput) {
        let searchTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => updateRatingList(1), 400);
        });
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentRole = btn.getAttribute('data-role');
            updateRatingList(1);
        });
    });

    // 3. Likes/Dislikes Voting Handler (Delegated)
    if (resultsContainer) {
        resultsContainer.addEventListener('click', async (e) => {
            const voteBtn = e.target.closest('.rate-user-btn');
            if (!voteBtn) return;

            e.preventDefault();

            const targetId = voteBtn.getAttribute('data-id');
            // We'll treat the support button as a 'like'
            const voteType = 'like'; 

            const formData = new FormData();
            formData.append('action', 'rating_vote');
            formData.append('id', targetId);
            formData.append('type', voteType);

            try {
                voteBtn.style.opacity = '0.5';
                voteBtn.style.pointerEvents = 'none';

                const response = await fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();

                if (res.success) {
                    // Update all scores and buttons for this user (could be in Top-3 and Table)
                    const userScores = document.querySelectorAll(`.score-display[data-id="${targetId}"]`);
                    userScores.forEach(el => {
                        // calculate net score if we want, or just show likes
                        // Since mockup shows heart and number, let's just show likes.
                        el.textContent = res.data.likes;
                    });

                    const userBtns = document.querySelectorAll(`.rate-user-btn[data-id="${targetId}"]`);
                    userBtns.forEach(btn => {
                        if (res.data.voted_type === 'like') {
                            btn.classList.add('voted');
                            btn.textContent = 'Поддержано';
                        } else {
                            btn.classList.remove('voted');
                            btn.textContent = 'Поддержать';
                        }
                    });

                } else {
                    if (res.data && res.data.message && res.data.message.includes('войти')) {
                        if (loginModal) {
                            loginModal.classList.add('is-open');
                            if (window.lenis) window.lenis.stop();
                        }
                    } else {
                        alert(res.data.message || 'Ошибка голосования.');
                    }
                }
            } catch (err) {
                console.error('Voting request error:', err);
            } finally {
                voteBtn.style.opacity = '1';
                voteBtn.style.pointerEvents = 'auto';
            }
        });
    }

    // Modal close controls
    if (closeLoginBtn && loginModal) {
        closeLoginBtn.addEventListener('click', () => {
            loginModal.classList.remove('is-open');
            if (window.lenis) window.lenis.start();
        });

        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.classList.remove('is-open');
                if (window.lenis) window.lenis.start();
            }
        });
    }

    // Initial load
    updateRatingList(1);
});
</script>

<?php get_footer(); ?>
