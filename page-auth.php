<?php
/**
 * Template Name: Авторизация и регистрация
 */

// Redirect logged-in users to profile
if (is_user_logged_in()) {
    wp_redirect(home_url('/profile/'));
    exit;
}

get_header(); ?>

<div class="auth-page">
    <div class="auth-card">
        <!-- Logo Header -->
        <div class="auth-card__logo-container">
            <a href="<?php echo home_url(); ?>" class="auth-logo">
                <div class="auth-logo__star">
                    <svg width="30" height="31" viewBox="0 0 74 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M36.6602 0L44.437 29.2244L73.3203 37.093L44.437 44.9616L36.6602 74.186L28.8834 44.9616L0 37.093L28.8834 29.2244L36.6602 0Z" fill="black"/>
                    </svg>
                </div>
                <span class="auth-logo__text">Gymnastics<span>Hub</span></span>
            </a>
        </div>

        <h1 class="auth-card__title">Вход в аккаунт</h1>
        <div class="auth-card__subtext" style="display: none;"></div>

        <!-- 1. LOGIN FORM -->
        <form class="auth-form active" id="loginForm" method="POST">
            <?php if (isset($_GET['redirect_to'])) : ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($_GET['redirect_to']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <input type="email" name="username" placeholder="Эл. почта" class="form-input" required>
            </div>
            <div class="form-group password-group">
                <input type="password" name="password" placeholder="Пароль" class="form-input" required>
                <button type="button" class="password-toggle">
                    <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>

            <div class="auth-message" style="margin-bottom: 15px; font-size: 14px; display: none;"></div>

            <div class="auth-form__actions">
                <a href="#register" class="btn btn--outline-dark btn--rounded" onclick="showAuthState('register'); return false;">У меня нет аккаунта</a>
                <button type="submit" class="btn btn--black btn--rounded">Войти</button>
            </div>

            <div class="auth-form__footer">
                <a href="#recovery" class="forgot-password" onclick="showAuthState('recovery'); return false;">Забыл пароль</a>
            </div>
        </form>

        <!-- 2. REGISTER FORM -->
        <form class="auth-form" id="registerForm" method="POST">
            <?php if (isset($_GET['redirect_to'])) : ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($_GET['redirect_to']); ?>">
            <?php endif; ?>
            <div class="role-selector">
                <div class="role-option active" data-role="gh_user">Пользователь</div>
                <div class="role-option" data-role="gh_club">Клуб</div>
                <div class="role-option" data-role="gh_coach">Тренер</div>
                <div class="role-option" data-role="gh_organizer">Организатор</div>
                <input type="hidden" name="role" id="selectedRole" value="gh_user">
            </div>
            <div class="form-group">
                <input type="text" name="full_name" placeholder="Имя / название организации" class="form-input" required>
            </div>
            <div class="form-group" id="regDescGroup" style="display: none;">
                <textarea name="description" placeholder="Короткое описание клуба или организации (для рейтинга)" class="form-input" style="width: 100%;
    background-color: #F2F2F2;
    border: 1px solid transparent;
    padding: 15px 25px;
    border-radius: 30px;
    font-size: 15px;
    font-family: inherit;
    outline: none;
    transition: 0.3s;
    resize: none;"></textarea>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Эл. почта" class="form-input" required>
            </div>
            <div class="form-group password-group">
                <input type="password" name="password" placeholder="Придумайте пароль" class="form-input" required>
                <button type="button" class="password-toggle">
                    <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <div class="form-group password-group">
                <input type="password" name="password_confirm" placeholder="Подтвердите пароль" class="form-input" required>
                <button type="button" class="password-toggle">
                    <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>

            <div class="auth-message" style="margin-bottom: 15px; font-size: 14px; display: none;"></div>

            <div class="auth-form__actions">
                <a href="#login" class="btn btn--outline-dark btn--rounded" onclick="showAuthState('login'); return false;">У меня уже есть аккаунт</a>
                <button type="submit" class="btn btn--black btn--rounded">Создать</button>
            </div>
        </form>

        <!-- 3. RECOVERY FORM (Placeholder for now) -->
        <form class="auth-form" id="recoveryForm" method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Эл. почта" class="form-input" required>
            </div>
            <div class="auth-form__actions">
                <a href="#login" class="btn btn--outline-dark btn--rounded" onclick="showAuthState('login'); return false;">← &nbsp; Назад</a>
                <button type="submit" class="btn btn--black btn--rounded">Восстановить</button>
            </div>
        </form>

        <!-- 4. RESET PASSWORD FORM (New) -->
        <form class="auth-form" id="resetForm" method="POST">
            <p class="auth-card__subtext" style="text-align: center; margin-bottom: 20px;">Введите код из письма и новый пароль</p>
            <input type="hidden" name="email" id="resetEmail">
            <div class="form-group">
                <input type="text" name="verify_code" maxlength="6" placeholder="000000" class="form-input" style="text-align: center; letter-spacing: 5px; font-weight: bold;" required>
            </div>
            <div class="form-group password-group">
                <input type="password" name="new_password" placeholder="Новый пароль" class="form-input" required>
                <button type="button" class="password-toggle">
                    <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-closed" style="display:none" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <div class="auth-message" style="margin-bottom: 15px; font-size: 14px; display: none;"></div>
            <div class="auth-form__actions">
                <button type="submit" class="btn btn--black btn--rounded" style="width: 100%;">Сменить пароль</button>
            </div>
        </form>

        <!-- 5. SUCCESS STATE -->
        <div class="recovery-success" style="display: none;">
            <div class="recovery-message">
                Заявка на регистрацию отправлена. Пожалуйста, ожидайте одобрения администратором.
            </div>
            <div class="auth-form__actions" style="justify-content: center;">
                <a href="<?php echo home_url('/'); ?>" class="btn btn--outline-dark btn--rounded" style="max-width: 200px;">На главную</a>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
