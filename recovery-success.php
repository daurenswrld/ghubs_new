<?php
/**
 * Template Name: Успешное восстановление пароля
 */
get_header(); ?>

<main class="main-content auth-page">
    <div class="auth-card">
        <!-- Logo Header -->
        <div class="auth-card__logo-container">
            <a href="<?php echo home_url('/'); ?>" class="auth-logo notranslate" translate="no">
                <svg width="190" height="30" viewBox="0 0 190 30" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                    <!-- Star -->
                    <g transform="translate(0, 3) scale(0.32)">
                        <path d="M36.6602 0L44.437 29.2244L73.3203 37.093L44.437 44.9616L36.6602 74.186L28.8834 44.9616L0 37.093L28.8834 29.2244L36.6602 0Z" fill="black"/>
                    </g>
                    <!-- Text -->
                    <text x="36" y="22" font-family="'Raleway', sans-serif" font-weight="600" font-size="22px" fill="black" letter-spacing="-0.02em">Gymnastics<tspan fill="#ff2d55">Hub</tspan></text>
                </svg>
            </a>
        </div>

        <h1 class="auth-card__title">Пароль изменен!</h1>
        
        <div class="recovery-success-content" style="text-align: center; margin-bottom: 30px;">
            <div class="success-icon" style="margin-bottom: 20px;">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="32" fill="#E8F5E9"/>
                    <path d="M20 32L28 40L44 24" stroke="#4CAF50" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p style="font-size: 18px; color: #333; line-height: 1.5;">
                Ваш пароль был успешно обновлен. <br>
                Теперь вы можете войти в свой аккаунт, используя новые данные.
            </p>
        </div>

        <div class="auth-form__actions" style="justify-content: center;">
            <a href="<?php echo home_url('/login/'); ?>" class="btn btn--black btn--rounded" style="width: 100%; text-align: center;">Войти в аккаунт</a>
        </div>
    </div>
</main>

<?php get_footer(); ?>
