<?php
/**
 * Theme Settings Page
 */

function gh_theme_settings_menu() {
    add_menu_page(
        'Настройки темы',
        'Gymnastics Hub',
        'edit_posts',
        'gh-theme-settings',
        'gh_theme_settings_page',
        'dashicons-admin-generic',
        60
    );
}
add_action('admin_menu', 'gh_theme_settings_menu');

function gh_theme_settings_init() {
    register_setting('gh_theme_settings_group', 'gh_contact_email');
    register_setting('gh_theme_settings_group', 'gh_contact_phone');
    register_setting('gh_theme_settings_group', 'gh_contact_address');
    register_setting('gh_theme_settings_group', 'gh_contact_instagram');
    register_setting('gh_theme_settings_group', 'gh_hero_title');
    register_setting('gh_theme_settings_group', 'gh_about_text');
}
add_action('admin_init', 'gh_theme_settings_init');

function gh_theme_settings_page() {
    ?>
    <div class="wrap">
        <h1>Настройки контактов Gymnastics Hub</h1>
        <form method="post" action="options.php">
            <?php settings_fields('gh_theme_settings_group'); ?>
            <?php do_settings_sections('gh_theme_settings_group'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Заголовок на главной (Hero)</th>
                    <td>
                        <textarea name="gh_hero_title" style="width: 100%;" rows="3"><?php echo esc_textarea(get_option('gh_hero_title')); ?></textarea>
                        <p class="description">Можно использовать &lt;br&gt; для переноса строки.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Email для связи</th>
                    <td><input type="email" name="gh_contact_email" value="<?php echo esc_attr(get_option('gh_contact_email')); ?>" class="regular-text" /></td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Номер телефона</th>
                    <td><input type="text" name="gh_contact_phone" value="<?php echo esc_attr(get_option('gh_contact_phone')); ?>" class="regular-text" /></td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Физический адрес</th>
                    <td><textarea name="gh_contact_address" class="regular-text" rows="3"><?php echo esc_textarea(get_option('gh_contact_address')); ?></textarea></td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Ссылка на Instagram</th>
                    <td><input type="url" name="gh_contact_instagram" value="<?php echo esc_attr(get_option('gh_contact_instagram')); ?>" class="regular-text" placeholder="https://instagram.com/..." /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Текст "О нас" (Главная)</th>
                    <td><textarea name="gh_about_text" style="width: 100%;" rows="10"><?php echo esc_textarea(get_option('gh_about_text')); ?></textarea></td>
                </tr>
            </table>
            
            <?php submit_button('Сохранить изменения'); ?>
        </form>
    </div>
    <?php
}

/**
 * Helper function to get theme option with default
 */
function gh_get_option($key, $default = '') {
    $value = get_option($key);
    return !empty($value) ? $value : $default;
}
