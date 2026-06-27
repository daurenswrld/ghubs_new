<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обсуждение | Gymnastics Hub</title>
    <meta name="description" content="Форум сообщества Gymnastics Hub — обсуждение мероприятий и тем гимнастики">
    
    <!-- Fonts: Raleway -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Smooth Scroll: Lenis -->
    <script src="https://unpkg.com/lenis@1.1.20/dist/lenis.min.js"></script>
</head>
<body class="light-theme loading">
    <!-- Premium Preloader -->
    <div id="preloader" class="preloader">
        <div class="preloader__content">
            <div class="preloader__star">
                <img src="img/logo.svg" alt="logo">
            </div>
            <div class="preloader__text">Gymnastics<span>Hub</span></div>
        </div>
    </div>
    <header class="main-header main-header--light">
        <div class="container container--wide">
            <div class="header__logo">
                <a href="index.html" class="logo">
                    <img src="img/logo-black.svg" alt="logo">
                    <span class="logo__text">Gymnastics<span>Hub</span></span>
                </a>
            </div>

            <nav class="header__nav">
                <ul class="nav-menu">
                    <li class="nav-menu__item dropdown">
                        <a href="#" class="nav-menu__link">Мероприятия <span class="arrow"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="events.html" class="dropdown-link">Все</a></li>
                            <li><a href="#" class="dropdown-link">Турниры</a></li>
                            <li><a href="#" class="dropdown-link">Сборы</a></li>
                            <li><a href="#" class="dropdown-link">Семинары & мастер-классы</a></li>
                        </ul>
                    </li>
                    <li class="nav-menu__item dropdown">
                        <a href="#" class="nav-menu__link active">Медиа <span class="arrow"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="gallery.html" class="dropdown-link">Галерея</a></li>
                            <li><a href="forum.html" class="dropdown-link active">Форум</a></li>
                        </ul>
                    </li>
                    <li class="nav-menu__item">
                        <a href="#" class="nav-menu__link">Объявления</a>
                    </li>
                </ul>
            </nav>

            <div class="header__actions">
                <a href="add-event.html" class="btn btn--outline-dark btn--sm">
                    <span class="plus-icon">+</span> Добавить мероприятие
                </a>
                <a href="login.html" class="btn btn--black btn--sm">
                    <img src="img/user-white.svg" alt="user" class="btn-icon"> Профиль
                </a>
            </div>

            <button class="menu-toggle" aria-label="Открыть меню">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <main class="main-content forum-single-page">
        <div class="container container--wide">

            <!-- ===================== FEATURED POST ===================== -->
            <div class="forum-post-card forum-post-card--top">
                <div class="forum-post-card__header">
                    <div class="user-avatar">
                        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    </div>
                    <span class="user-name">Кожыбай Байталасов</span>
                    <span class="post-time">16 часов назад</span>
                </div>
                <h1 class="forum-post-card__title">Сайт рыбатекст поможет дизайнеру, верстальщику</h1>
                <p class="forum-post-card__body">
                    Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском языке, а начинающему оратору отточить навык публичных выступлений в домашних условиях.
                </p>
                <div class="forum-post-card__footer">
                    <div class="action-group">
                        <button class="action-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                            14
                        </button>
                        <button class="action-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                            3
                        </button>
                    </div>
                    <button class="btn-reply">Ответить</button>
                    <div class="comments-count">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        3 ответов
                    </div>
                </div>
            </div>

            <!-- ===================== COMMENTS ===================== -->
            <div class="forum-comments">

                <!-- ── Comment 1 (с вложенными ответами) ── -->
                <div class="comment-node">
                    <div class="forum-post-card">
                        <div class="forum-post-card__header">
                            <div class="user-avatar">
                                <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            </div>
                            <span class="user-name">Кожыбай Байталасов</span>
                            <span class="post-time">16 часов назад</span>
                        </div>
                        <p class="forum-post-card__excerpt">
                            Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском языке, а начинающему оратору отточить навык публичных выступлений в домашних условиях.
                        </p>
                        <div class="forum-post-card__footer">
                            <div class="action-group">
                                <button class="action-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                    14
                                </button>
                                <button class="action-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                    3
                                </button>
                            </div>
                            <button class="btn-reply">Ответить</button>
                        </div>
                    </div>

                    <!-- Nested replies for comment 1 -->
                    <div class="comment-node__replies">
                        <div class="comment-reply">
                            <div class="forum-post-card">
                                <div class="forum-post-card__header">
                                    <div class="user-avatar">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                    </div>
                                    <span class="user-name">Кожыбай Байталасов</span>
                                    <span class="post-time">16 часов назад</span>
                                </div>
                                <p class="forum-post-card__excerpt">
                                    Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском языке, а начинающему оратору отточить навык публичных выступлений в домашних условиях.
                                </p>
                                <div class="forum-post-card__footer">
                                    <div class="action-group">
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                            14
                                        </button>
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                            3
                                        </button>
                                    </div>
                                    <button class="btn-reply">Ответить</button>
                                </div>
                            </div>
                        </div>

                        <div class="comment-reply">
                            <div class="forum-post-card">
                                <div class="forum-post-card__header">
                                    <div class="user-avatar">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                    </div>
                                    <span class="user-name">Кожыбай Байталасов</span>
                                    <span class="post-time">16 часов назад</span>
                                </div>
                                <p class="forum-post-card__excerpt">
                                    Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском языке, а начинающему оратору отточить навык публичных выступлений в домашних условиях.
                                </p>
                                <div class="forum-post-card__footer">
                                    <div class="action-group">
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                            14
                                        </button>
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                            3
                                        </button>
                                    </div>
                                    <button class="btn-reply">Ответить</button>
                                </div>
                            </div>
                        </div>

                        <button class="btn-load-more-comments">
                            Еще 5 ответов
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                    </div>
                </div>

                <!-- ── Comment 2 (с вложенными ответами) ── -->
                <div class="comment-node">
                    <div class="forum-post-card">
                        <div class="forum-post-card__header">
                            <div class="user-avatar">
                                <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            </div>
                            <span class="user-name">Кожыбай Байталасов</span>
                            <span class="post-time">16 часов назад</span>
                        </div>
                        <p class="forum-post-card__excerpt">
                            Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском языке, а начинающему оратору отточить навык публичных выступлений в домашних условиях.
                        </p>
                        <div class="forum-post-card__footer">
                            <div class="action-group">
                                <button class="action-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                    14
                                </button>
                                <button class="action-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                    3
                                </button>
                            </div>
                            <button class="btn-reply">Ответить</button>
                        </div>
                    </div>

                    <!-- Nested replies for comment 2 -->
                    <div class="comment-node__replies">
                        <div class="comment-reply">
                            <div class="forum-post-card">
                                <div class="forum-post-card__header">
                                    <div class="user-avatar">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                    </div>
                                    <span class="user-name">Кожыбай Байталасов</span>
                                    <span class="post-time">16 часов назад</span>
                                </div>
                                <p class="forum-post-card__excerpt">
                                    Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском языке.
                                </p>
                                <div class="forum-post-card__footer">
                                    <div class="action-group">
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                            14
                                        </button>
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                            3
                                        </button>
                                    </div>
                                    <button class="btn-reply">Ответить</button>
                                </div>
                            </div>
                        </div>

                        <div class="comment-reply">
                            <div class="forum-post-card">
                                <div class="forum-post-card__header">
                                    <div class="user-avatar">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                    </div>
                                    <span class="user-name">Кожыбай Байталасов</span>
                                    <span class="post-time">16 часов назад</span>
                                </div>
                                <p class="forum-post-card__excerpt">
                                    Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев.
                                </p>
                                <div class="forum-post-card__footer">
                                    <div class="action-group">
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                            14
                                        </button>
                                        <button class="action-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zM17 2h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                            3
                                        </button>
                                    </div>
                                    <button class="btn-reply">Ответить</button>
                                </div>
                            </div>
                        </div>

                        <button class="btn-load-more-comments">
                            Еще 5 ответов
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                    </div>
                </div>

            </div>
            <!-- /forum-comments -->

        </div>
    </main>

    <footer class="main-footer">
        <div class="container container--wide">
            <div class="footer__top">
                <div class="footer__col footer__promo">
                    <h2 class="footer__tagline">
                        Будем рады <br>
                        работать <br>
                        <span class="italic">вместе</span>
                    </h2>
                    <div class="publish-block">
                        <a href="add-event.html" class="publish-btn">
                            <span class="circle-arrow">
                                <img src="img/arrow-up-right.svg" alt="arrow">
                            </span>
                            <span class="btn-text">ОПУБЛИКОВАТЬ МЕРОПРИЯТИЕ</span>
                        </a>
                    </div>
                </div>
                <div class="footer__col footer__contacts">
                    <div class="contact-item">
                        <span class="contact-label">Почта</span>
                        <a href="mailto:лала@gmail.com" class="contact-value">лала@gmail.com</a>
                    </div>
                    <div class="contact-item">
                        <span class="contact-label">Контакты</span>
                        <a href="tel:+77051234353" class="contact-value">+7 705 123 4353</a>
                    </div>
                    <div class="contact-item">
                        <span class="contact-label">Адрес</span>
                        <span class="contact-value">г. Астана, ул. Пушкина, <br>дом Колатушкина</span>
                    </div>
                </div>
                <div class="footer__col footer__socials">
                    <a href="https://instagram.com" class="social-link instagram" target="_blank">
                        <img src="img/instagram.svg" alt="Instagram" class="social-icon">
                        <span>INSTAGRAM</span>
                    </a>
                </div>
            </div>
            <div class="footer__bottom">
                <a href="https://ziz.kz" class="copyright" target="_blank">
                    Разработка и поддержка сайтов <span>ZIZ INC</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- Mobile Navigation Overlay -->
    <nav class="mobile-nav">
        <button class="mobile-nav__close" aria-label="Закрыть меню">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <ul class="mobile-nav__menu">
            <li class="mobile-nav__item">
                <span class="mobile-nav__link has-dropdown">
                    Мероприятия <span class="mobile-arrow">›</span>
                </span>
                <div class="mobile-dropdown">
                    <a href="events.html">Все</a>
                    <a href="#">Турниры</a>
                    <a href="#">Сборы</a>
                    <a href="#">Семинары & мастер-классы</a>
                </div>
            </li>
            <li class="mobile-nav__item">
                <span class="mobile-nav__link has-dropdown">
                    Медиа <span class="mobile-arrow">›</span>
                </span>
                <div class="mobile-dropdown">
                    <a href="gallery.html">Галерея</a>
                    <a href="forum.html">Форум</a>
                </div>
            </li>
            <li class="mobile-nav__item">
                <a href="#" class="mobile-nav__link">Объявления</a>
            </li>
        </ul>
        <div class="mobile-nav__actions">
            <a href="add-event.html" class="btn btn--outline-dark">+ Добавить мероприятие</a>
            <a href="login.html" class="btn btn--black">Войти</a>
        </div>
    </nav>

    <script src="js/main.js"></script>
</body>
</html>
