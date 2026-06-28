<?php
/**
 * Profanity Filter for GymnasticsHub
 */

function gh_is_profane($text) {
    // Список запрещенных слов (можно дополнять)
    $bad_words = array(
        // Оскорбления и мат (Русский)
        'пидор', 'хуй', 'пизда', 'ебать', 'сука', 'гандон', 'чмо', 'уебок', 'бля', 'шлюха', 'проститутка', 'мразь',
        'хуесос', 'дрочила', 'говнюк', 'залупа', 'манда', 'пидрила', 'пидорас', 'сучара', 'ебло', 'хер',
        'блять', 'еблан', 'долбоеб', 'долбоёб', 'пиздец', 'хуйня', 'мудак', 'ублюдок', 'тварь', 'скотина',
        'шмара', 'гондон', 'ебанат', 'педик', 'лох', 'гнида', 'падла', 'петух', 'даун', 'дебил', 'урод', 'соска',

        // Оскорбления и мат (Казахский)
        'қотақ', 'қотақбас', 'ам', 'көт', 'жәлеп', 'қаншық', 'шешең', 'шешенің', 'сігу', 'сік', 'сігіл', 'сігінді', 
        'көтбас', 'көтібас', 'мал', 'топас', 'ешек', 'мәл', 'жәлептер', 'қотағым', 'амыңды', 'көтіңді', 'амшық',

        // Оскорбления и мат (Английский - защита от спам-ботов)
        'fuck', 'shit', 'bitch', 'asshole', 'cunt', 'dick', 'cock', 'pussy', 'slut', 'whore', 'bastard', 
        'motherfucker', 'faggot', 'nigger', 'nigga', 'crap', 'bullshit', 'douchebag', 'jerk', 'moron',

        // Транслит и частые подмены (пользователи часто пишут мат латиницей)
        'xyu', 'pizda', 'ebat', 'cyka', 'blyat', 'pidor', 'chmo', 'gandon', 'xep', 'zaeb', 'suka', 'blia',

        // Спам, реклама, ставки и 18+ (частый мусор при авторегистрациях на сайтах)
        'casino', 'казино', '1xbet', 'pinup', 'betting', 'ставки', 'ставк', 'bonus', 'бонус', 'promo', 'промо', 
        'free', 'бесплатно', 'viagra', 'виагра', 'porno', 'порно', 'xxx', 'sex', 'секс', 'эскорт', 'escort', 
        'crypto', 'крипта', 'bitcoin', 'биткоин', 'invest', 'инвест', 'income', 'заработок', 'buy', 'купить',

        // Системные, фейковые и зарезервированные имена (English)
        'admin', 'administrator', 'moderator', 'support', 'help', 'gymnasticshub', 'official', 'verified',
        'root', 'system', 'sysadmin', 'webmaster', 'info', 'test', 'guest', 'user', 'anonymous', 'bot',
        'owner', 'manager', 'director', 'security', 'staff', 'service', 'billing', 'sales', 'marketing',
        'noreply', 'no-reply', 'superuser', 'dev', 'developer',

        // Системные, фейковые и зарезервированные имена (Русский / Казахский)
        'админ', 'администратор', 'модератор', 'поддержка', 'помощь', 'официальный',
        'рут', 'система', 'вебмастер', 'инфо', 'тест', 'гость', 'пользователь', 'аноним', 'бот',
        'менеджер', 'директор', 'охрана', 'служба', 'персонал', 'разработчик', 'создатель', 'владелец',
        'әкімші', 'басқарушы', 'қолдау', 'көмек', 'ресми', 'басқару', 'жүйе'
    );

    $text = mb_strtolower($text);

    // 1. Check for bad words
    foreach ($bad_words as $word) {
        if (mb_strpos($text, $word) !== false) {
            return array('error' => true, 'message' => 'Имя содержит недопустимые слова.');
        }
    }

    // 2. Check for word count (Max 10 words)
    $words = preg_split('/\s+/', trim($text));
    if (count($words) > 10) {
        return array('error' => true, 'message' => 'Имя должно содержать не более 10 слов.');
    }

    // 3. Check for total length (Max 100 chars)
    if (mb_strlen($text) > 100) {
        return array('error' => true, 'message' => 'Имя слишком длинное (макс. 100 символов).');
    }

    return array('error' => false);
}
