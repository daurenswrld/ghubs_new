<?php
/**
 * Custom comments template for Forum
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <div class="comments-list">
            <?php
            wp_list_comments(array(
                'style'       => 'div',
                'short_ping'  => true,
                'callback'    => 'gh_forum_comment_callback', // Custom callback for forum design
                'max_depth'   => 3,
            ));
            ?>
        </div>

        <?php the_comments_navigation(); ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments">Обсуждение закрыто.</p>
    <?php endif; ?>

    <?php
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');

    comment_form(array(
        'title_reply' => 'Оставить ответ',
        'title_reply_to' => 'Ответить %s',
        'class_form' => 'forum-comment-form',
        'logged_in_as' => '', // Hide the "Logged in as" message
        'comment_field' => '<div class="comment-form-field"><textarea id="comment" name="comment" cols="45" rows="5" placeholder="Ваш ответ..." aria-required="true"></textarea></div>',
        'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="btn btn--black btn--full">%4$s</button>',
        'submit_field' => '<div class="form-submit">%1$s %2$s</div>',
    ));
    ?>

</div>
