<?php
/**
 * Template Name: Подтверждение Email
 */

if (is_user_logged_in()) {
    wp_redirect(home_url('/profile/'));
    exit;
}

// Get email from URL
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

// If no email, redirect back to register
if (!$email) {
    wp_redirect(home_url('/login/#register'));
    exit;
}

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

        <h1 class="auth-card__title">Подтверждение почты</h1>
        <p class="verify-subtext" style="text-align: center; color: #777; margin-bottom: 25px;">
            Мы отправили 6-значный код на вашу почту <br>
            <strong><?php echo esc_html($email); ?></strong>
            <?php 
            $pending = get_transient('gh_pending_reg_' . md5($email));
            if (!$pending) {
                echo '<p class="error">Сессия истекла. Попробуйте еще раз.</p>';
            }
            ?>
        </p>

        <form class="auth-form active" id="verifyEmailForm" method="POST">
            <!-- Hidden email field for the AJAX handler -->
            <input type="hidden" name="email" value="<?php echo esc_attr($email); ?>">
            
            <div class="form-group">
                <input type="text" name="verify_code" maxlength="6" placeholder="000000" class="form-input code-input" style="text-align: center; letter-spacing: 5px; font-size: 24px; font-weight: bold;" required autofocus>
            </div>

            <div class="auth-message" style="margin-bottom: 15px; font-size: 14px; display: none;"></div>

            <div class="auth-form__actions" style="justify-content: center;">
                <button type="submit" class="btn btn--black btn--rounded" style="width: 100%;">Подтвердить</button>
            </div>

            <div class="auth-form__footer" style="text-align: center; margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                <a href="#" id="resendCode" style="color: #000; font-weight: 600; font-size: 14px; text-decoration: underline;">Отправить код повторно</a>
            </div>
        </form>
    </div>
</main>

<?php get_footer(); ?>
