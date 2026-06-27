<?php
/**
 * SMTP Configuration for GymnasticsHub
 * Using mail.ru credentials (TLS version)
 */

add_action('phpmailer_init', 'gh_setup_smtp');
function gh_setup_smtp($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.mail.ru';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587; // Changed to 587
    $phpmailer->Username   = 'jauapberme@mail.ru';
    $phpmailer->Password   = 'CcrlGhmm8dYz9yc8OAYd';
    $phpmailer->SMTPSecure = 'tls'; // Changed to tls
    $phpmailer->From       = 'jauapberme@mail.ru';
    $phpmailer->FromName   = 'GymnasticsHub';
    
    // Debugging (Uncomment to see errors in logs if needed)
    // $phpmailer->SMTPDebug = 2; 
}

// Ensure emails are sent as HTML
add_filter('wp_mail_content_type', function() {
    return 'text/html';
});
