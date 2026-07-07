    <?php if (!is_page_template('page-auth.php') && !is_page_template('verify-email.php') && !is_page_template('recovery-success.php')) : ?>
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
                        <a href="<?php echo esc_url(home_url('/add-event/')); ?>" class="publish-btn">
                            <span class="circle-arrow">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-up-right.svg" alt="arrow">
                            </span>
                            <span class="btn-text">ДОБАВИТЬ МЕРОПРИЯТИЕ</span>
                        </a>
                    </div>
                </div>

                <div class="footer__col footer__contacts">
                    <div class="contact-item">
                        <span class="contact-label">Почта</span>
                        <?php $email = get_option('gh_contact_email', 'info@gymnasticshub.kz'); ?>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-value"><?php echo esc_html($email); ?></a>
                    </div>
                    <div class="contact-item">
                        <span class="contact-label">Контакты</span>
                        <?php $phone = get_option('gh_contact_phone', '+7 705 123 4353'); ?>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" class="contact-value"><?php echo esc_html($phone); ?></a>
                    </div>
                    <div class="contact-item">
                        <span class="contact-label">Адрес</span>
                        <span class="contact-value"><?php echo nl2br(esc_html(get_option('gh_contact_address', 'г. Астана, ул. Пушкина'))); ?></span>
                    </div>
                </div>

                <div class="footer__col footer__socials">
                    <div class="contact-item">
                        <span class="contact-label">Социальные сети</span>
                        <?php $insta = get_option('gh_contact_instagram', 'https://instagram.com'); ?>
                        <a href="<?php echo esc_url($insta); ?>" class="contact-value" target="_blank">Instagram</a>
                    </div>
                </div>
            </div>

            <div class="footer__bottom">
                <a href="https://ziz.kz" class="copyright" target="_blank">
                    Разработка и поддержка сайтов <span>ZIZ INC</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- GTranslate Widget -->
    <div class="gtranslate-fixed-left">
        <?php echo do_shortcode('[gtranslate]'); ?>
    </div>

    <!-- AI Assistant Widget -->
    <div class="ai-widget-container">
        <div class="ai-tooltip">Привет! Я Стефа 👋 <br> Чем могу помочь?</div>
        <a href="#" class="ai-widget" title="AI Ассистент">
            <img src="<?php echo get_template_directory_uri(); ?>/img/ai-btn.png" alt="AI">
        </a>
    </div>

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
                    <a href="<?php echo esc_url(home_url('/events/')); ?>">Все</a>
                    <?php 
                    $mobile_types = get_terms(array('taxonomy' => 'event_type', 'hide_empty' => false));
                    $mobile_types = gh_sort_event_types($mobile_types);
                    foreach ($mobile_types as $m_type) : ?>
                        <a href="<?php echo esc_url(home_url('/events/?event_type=' . $m_type->slug)); ?>"><?php echo $m_type->name; ?></a>
                    <?php endforeach; ?>
                </div>
            </li>
            <li class="mobile-nav__item">
                <span class="mobile-nav__link has-dropdown">
                    Медиа <span class="mobile-arrow">›</span>
                </span>
                <div class="mobile-dropdown">
                    <a href="<?php echo esc_url(home_url('/gallery/')); ?>">Галерея</a>
                    <a href="<?php echo esc_url(home_url('/forum/')); ?>">Форум</a>
                </div>
            </li>
            <li class="mobile-nav__item">
                <a href="<?php echo esc_url(home_url('/ads/')); ?>" class="mobile-nav__link">Объявления</a>
            </li>
            <li class="mobile-nav__item">
                <a href="<?php echo esc_url(home_url('/rating/')); ?>" class="mobile-nav__link">Рейтинг</a>
            </li>
            <li class="mobile-nav__item">
                <a href="#" class="mobile-nav__link">Магазин</a>
            </li>
        </ul>
        <div class="mobile-nav__actions">
            <a href="<?php echo esc_url(home_url('/add-event/')); ?>" class="btn btn--outline-dark">+ Добавить мероприятие</a>
            <?php if (is_user_logged_in()) : 
                $current_user = wp_get_current_user();
                $mobile_avatar = get_user_meta($current_user->ID, 'gh_avatar', true);
                if (!$mobile_avatar) {
                    $mobile_avatar = get_template_directory_uri() . '/img/user-white.svg';
                }
            ?>
                <a href="<?php echo esc_url(home_url('/profile/')); ?>" class="btn btn--black btn--login">
                    <img src="<?php echo esc_url($mobile_avatar); ?>" alt="user" class="btn-icon user-avatar-img" style="border-radius: 50%; width: 20px; height: 20px; object-fit: cover; margin-right: 8px;"> 
                    Профиль
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/login/')); ?>" class="btn btn--black">Войти</a>
            <?php endif; ?>
        </div>
    </nav>
    <?php endif; ?>

    <script>
    // Improved Preloader Logic to prevent scroll flickers
    (function() {
        var hidePre = function() {
            var pre = document.getElementById('preloader');
            if (pre && !pre.classList.contains('is-hidden')) {
                pre.classList.add('is-hidden');
                
                // 1. First fade out visually
                pre.style.transition = 'opacity 0.8s ease-in-out';
                pre.style.opacity = '0';
                
                // 2. Wait for fade to finish before enabling scroll
                setTimeout(function() { 
                    pre.style.display = 'none'; 
                    document.body.classList.remove('loading');
                    // Trigger a scroll event to notify Lenis or other scripts
                    window.dispatchEvent(new Event('resize'));
                }, 800);
            }
        };
        
        // Hide after 3000ms delay to ensure brand visibility and animation finish
        if (document.readyState === 'complete') { 
            setTimeout(hidePre, 3000); 
        } else { 
            window.addEventListener('load', function() {
                setTimeout(hidePre, 3000);
            }); 
        }
        
        // Absolute safety timeout
        setTimeout(hidePre, 4000); 
    })();
    </script>
    <!-- PWA Install Modal -->
    <div class="pwa-install-modal-overlay" id="pwaInstallModalOverlay">
        <div class="pwa-install-modal">
            <button class="pwa-install-modal__close" id="pwaInstallClose" aria-label="Закрыть">&times;</button>
            <div class="pwa-install-modal__icon-wrapper">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/img/app-icon-192.png'); ?>" alt="Gymnastics Hub Icon">
            </div>
            <h3 class="pwa-install-modal__title">Установите Gymnastics Hub</h3>
            <p class="pwa-install-modal__desc">Добавьте наше приложение на рабочий стол для быстрого доступа к мероприятиям и объявлениям!</p>
            
            <div class="pwa-install-tabs">
                <button class="pwa-install-tab-btn" data-target="pwa-step-ios">iOS (Safari)</button>
                <button class="pwa-install-tab-btn" data-target="pwa-step-android">Android</button>
                <button class="pwa-install-tab-btn" data-target="pwa-step-pc">ПК / Ноутбук</button>
            </div>

            <div class="pwa-install-content">
                <div class="pwa-install-step" id="pwa-step-ios">
                    <ol>
                        <li>Нажмите кнопку <strong>«Поделиться»</strong> в нижней панели Safari (иконка квадрата со стрелкой вверх).</li>
                        <li>В открывшемся меню выберите пункт <strong>«На экран "Домой"»</strong>.</li>
                        <li>Нажмите кнопку <strong>«Добавить»</strong> в верхнем правом углу.</li>
                    </ol>
                </div>
                <div class="pwa-install-step" id="pwa-step-android">
                    <ol>
                        <li>Нажмите кнопку <strong>«Установить»</strong> ниже (или нажмите на три точки в углу браузера).</li>
                        <li>Выберите пункт <strong>«Установить приложение»</strong> (или «Добавить на гл. экран»).</li>
                        <li>Подтвердите установку.</li>
                    </ol>
                </div>
                <div class="pwa-install-step" id="pwa-step-pc">
                    <ol>
                        <li>Нажмите на иконку <strong>«Установить»</strong> (монитор со стрелкой) в правой части адресной строки браузера.</li>
                        <li>Во всплывающем окне подтвердите установку, нажав кнопку <strong>«Установить»</strong>.</li>
                    </ol>
                </div>
            </div>

            <button class="pwa-install-modal__action-btn" id="pwaInstallActionBtn">Установить</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var overlay = document.getElementById('pwaInstallModalOverlay');
        var closeBtn = document.getElementById('pwaInstallClose');
        var actionBtn = document.getElementById('pwaInstallActionBtn');
        var tabButtons = document.querySelectorAll('.pwa-install-tab-btn');
        var steps = document.querySelectorAll('.pwa-install-step');
        var deferredPrompt = null;

        // Listen for BeforeInstallPrompt event
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
        });

        // Detect Platform to pre-select correct tab
        function detectPlatform() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return 'ios';
            }
            if (/android/i.test(userAgent)) {
                return 'android';
            }
            return 'pc';
        }

        function selectTab(platform) {
            var targetId = 'pwa-step-' + platform;
            
            tabButtons.forEach(function(btn) {
                if (btn.getAttribute('data-target') === targetId) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            steps.forEach(function(step) {
                if (step.id === targetId) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Adjust CTA button text/visibility
            if (platform === 'ios') {
                actionBtn.textContent = 'Понятно';
            } else {
                actionBtn.textContent = 'Установить';
            }
        }

        // Initialize Tab
        var userPlatform = detectPlatform();
        selectTab(userPlatform);

        // Bind Tab Clicks
        tabButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = btn.getAttribute('data-target');
                var platform = targetId.replace('pwa-step-', '');
                selectTab(platform);
            });
        });

        // Close logic
        function closeModal() {
            overlay.classList.remove('is-open');
            localStorage.setItem('pwa_prompt_dismissed', 'true');
        }

        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeModal();
            }
        });

        // Action button click
        actionBtn.addEventListener('click', function() {
            var activeTab = document.querySelector('.pwa-install-tab-btn.active');
            var platform = activeTab.getAttribute('data-target').replace('pwa-step-', '');

            if (platform === 'ios') {
                closeModal();
            } else if (platform === 'android' || platform === 'pc') {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function(choiceResult) {
                        deferredPrompt = null;
                        closeModal();
                    });
                } else {
                    alert('Пожалуйста, воспользуйтесь стандартной инструкцией во вкладке выше для вашего браузера.');
                    closeModal();
                }
            }
        });

        // Check standalone mode and localStorage to show modal after 5 seconds
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        var isDismissed = localStorage.getItem('pwa_prompt_dismissed') === 'true';

        if (!isStandalone && !isDismissed) {
            setTimeout(function() {
                overlay.classList.add('is-open');
            }, 3000);
        }
    });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
