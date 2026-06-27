<?php 
/**
 * Template Name: Добавить тему
 */
if ( !is_user_logged_in() ) {
    $redirect_url = urlencode(home_url('/add-topic/'));
    wp_redirect( home_url('/login/?redirect_to=' . $redirect_url) );
    exit;
}
get_header(); 
?>

    <main class="main-content add-album-page">
        <div class="container container--narrow">
            <div class="add-album-header">
                <h1 class="page-title">Начать беседу</h1>
                <p class="page-subtitle">Поделитесь своим вопросом или мнением с сообществом Gymnastics Hub.</p>
            </div>

            <form class="add-album-form" id="addTopicForm">
                <div class="form-group">
                    <input type="text" name="title" class="form-input--pill" placeholder="Заголовок" required id="topicTitle">
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <textarea name="content" class="form-textarea--pill" placeholder="Описание вашей темы..." style="min-height: 200px;" required id="topicDesc"></textarea>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn--black btn--pill">
                        Опубликовать &rarr;
                    </button>
                </div>
                <div id="formMessage" style="margin-top: 20px;"></div>
            </form>
        </div>
    </main>

    <style>
        .global-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(5px);
        }
        .global-modal-overlay.is-open {
            display: flex;
            opacity: 1;
        }
        .global-modal {
            background: #fff;
            padding: 40px;
            border-radius: 30px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            transform: translateY(20px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: #000;
        }
        .global-modal-overlay.is-open .global-modal {
            transform: translateY(0);
        }
        .global-modal h3 {
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .global-modal p {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 30px;
        }
    </style>

    <div class="global-modal-overlay" id="successModal">
        <div class="global-modal">
            <div class="global-modal__icon" style="background: #000; color: #fff; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 30px;">✓</div>
            <h3>Отправлено на модерацию</h3>
            <p>Ваша тема успешно создана! Она появится на форуме сразу после того, как администратор проверит и утвердит её.</p>
            <div style="margin-top: 25px;">
                <a href="<?php echo esc_url(home_url('/forum/')); ?>" class="btn btn--black btn--pill">Вернуться на форум</a>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const topicForm = document.getElementById('addTopicForm');
    const successModal = document.getElementById('successModal');
    const formMessage = document.getElementById('formMessage');
    
    if (topicForm) {
        topicForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = topicForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Публикация...';
            formMessage.innerHTML = '';

            const formData = new FormData(topicForm);
            formData.append('action', 'gh_add_forum_topic');

            try {
                const response = await fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();
                
                if (res.success) {
                    successModal.classList.add('is-open');
                    topicForm.reset();
                } else {
                    formMessage.innerHTML = `<div style="color: #ff4d4d; font-weight: 600;">${res.data}</div>`;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                formMessage.innerHTML = `<div style="color: #ff4d4d; font-weight: 600;">Произошла ошибка при отправке.</div>`;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Close modal on overlay click
    if (successModal) {
        successModal.addEventListener('click', (e) => {
            if (e.target === successModal) {
                successModal.classList.remove('is-open');
            }
        });
    }
});
</script>

<?php get_footer(); ?>
