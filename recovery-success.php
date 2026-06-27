<?php
/**
 * Template Name: Успешное восстановление пароля
 */
get_header(); ?>

<main class="main-content auth-page">
    <div class="auth-card">
        <!-- Logo Header -->
        <div class="auth-card__logo-container">
            <a href="<?php echo home_url('/'); ?>" class="auth-logo">
                <div class="auth-logo__star">
                    <svg width="30" height="31" viewBox="0 0 74 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M36.6602 0L44.437 29.2244L73.3203 37.093L44.437 44.9616L36.6602 74.186L28.8834 44.9616L0 37.093L28.8834 29.2244L36.6602 0Z" fill="black"/>
                    </svg>
                </div>
                <span class="auth-logo__text">Gymnastics<span>Hub</span></span>
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
