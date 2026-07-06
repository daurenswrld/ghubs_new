<?php
/**
 * Template Name: Добавить объявление
 */
if (!is_user_logged_in()) {
    $redirect_url = home_url('/login/?redirect_to=' . urlencode(get_permalink()));
    wp_redirect($redirect_url);
    exit;
}
get_header(); ?>

    <main class="main-content add-ad-page">
        <div class="container container--ad-form">
            <div class="add-ad-header">
                <h1 class="page-title">Добавление объявлений</h1>
                
                <div class="stepper">
                    <div class="step active">
                        <span class="step-number">1</span>
                        <span class="step-text">Основная информация</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number">2</span>
                        <span class="step-text">Проверка и публикация</span>
                    </div>
                </div>

                <p class="page-subtitle">Заполните данные для публикации вашего объявления.</p>
            </div>

            <form class="add-ad-form" id="addAdFormDynamic" enctype="multipart/form-data">
                <!-- Image Upload Box -->
                <div class="upload-box-v2--dashed" id="adUploadBox" style="cursor: pointer;">
                    <div class="upload-box__icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                    <div class="upload-box__text">
                        <h4 id="uploadTitle">Загрузить фотографии</h4>
                        <p>Перетащите или нажмите на данную область, чтобы загрузить файлы</p>
                    </div>
                    <input type="file" name="ad_photos[]" id="adFileField" hidden accept="image/*" multiple>
                </div>

                <!-- Preview Container -->
                <div id="adPhotoPreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; margin-bottom: 25px;"></div>

                <!-- Title -->
                <div class="form-group">
                    <input type="text" name="ad_title" class="form-input-v2" placeholder="Название" required>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <textarea name="ad_content" class="form-input-v2" placeholder="Описание" rows="6" style="resize: vertical;" required></textarea>
                </div>

                <!-- Contacts Row -->
                <div class="form-row-v2" style="margin-bottom: 30px;">
                    <div class="icon-input-v2">
                        <input type="tel" name="ad_phone" class="form-input-v2" placeholder="Номер телефона" required>
                        <img src="<?php echo get_template_directory_uri(); ?>/img/phone-gray.svg" alt="" class="field-icon-v2">
                    </div>
                    <div class="icon-input-v2">
                        <input type="email" name="ad_email" class="form-input-v2" placeholder="Почта" required>
                        <img src="<?php echo get_template_directory_uri(); ?>/img/mail-gray.svg" alt="" class="field-icon-v2">
                    </div>
                </div>

                <div class="form-footer-v2">
                    <button type="submit" class="btn btn--black btn--pill" id="submitAdBtn" style="padding: 18px 45px;">
                        Отправить на модерацию &rarr;
                    </button>
                    <div id="adFormMessage" style="margin-top: 15px; font-weight: 500; text-align: center;"></div>
                </div>
            </form>
        </div>
    </main>

    <!-- Success Modal -->
    <div class="global-modal-overlay" id="successModalAds">
        <div class="global-modal">
            <div class="global-modal__icon" style="background: #E8F5E9; color: #4CAF50; width: 60px; height: 60px; line-height: 60px; border-radius: 50%; margin: 0 auto 20px; font-size: 30px; text-align: center;">✓</div>
            <h3 style="font-size: 24px; margin-bottom: 10px; text-align: center;">Отправлено!</h3>
            <p style="color: #666; margin-bottom: 30px; text-align: center;">Ваше объявление появится на сайте сразу после проверки администратором.</p>
            <div class="btn-group" style="text-align: center;">
                <a href="<?php echo esc_url(home_url('/ads/')); ?>" class="btn btn--black btn--pill">Вернуться к объявлениям</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('addAdFormDynamic');
            const uploadBox = document.getElementById('adUploadBox');
            const fileInput = document.getElementById('adFileField');
            const uploadTitle = document.getElementById('uploadTitle');
            const previewContainer = document.getElementById('adPhotoPreview');
            const message = document.getElementById('adFormMessage');
            const submitBtn = document.getElementById('submitAdBtn');

            let selectedFiles = [];

            if (uploadBox && fileInput) {
                uploadBox.addEventListener('click', () => fileInput.click());
                
                fileInput.addEventListener('change', (e) => {
                    const newFiles = Array.from(fileInput.files);
                    const MAX_FILES = 10;
                    const MAX_SIZE = 5 * 1024 * 1024; // 5MB

                    let errors = [];

                    newFiles.forEach(file => {
                        if (selectedFiles.length >= MAX_FILES) {
                            if (!errors.includes('Максимум 10 фото')) errors.push('Максимум 10 фото');
                            return;
                        }
                        if (file.size > MAX_SIZE) {
                            errors.push(`Файл ${file.name} слишком большой (макс. 5МБ)`);
                            return;
                        }
                        selectedFiles.push(file);
                    });

                    if (errors.length > 0) {
                        alert(errors.join('\n'));
                    }

                    renderPreviews();
                    fileInput.value = ''; // Reset input to allow re-selecting same files
                });
            }

            function renderPreviews() {
                previewContainer.innerHTML = '';
                if (selectedFiles.length === 0) {
                    uploadTitle.textContent = "Загрузить фотографии";
                    return;
                }

                uploadTitle.textContent = "Файлов выбрано: " + selectedFiles.length;

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.style.cssText = `
                            position: relative;
                            width: 100px;
                            height: 100px;
                            border-radius: 15px;
                            overflow: hidden;
                            border: 1px solid #eee;
                        `;
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '&times;';
                        removeBtn.type = 'button';
                        removeBtn.style.cssText = `
                            position: absolute;
                            top: 5px;
                            right: 5px;
                            background: rgba(0,0,0,0.6);
                            color: #fff;
                            border: none;
                            border-radius: 50%;
                            width: 22px;
                            height: 22px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            cursor: pointer;
                            font-size: 18px;
                            line-height: 1;
                        `;
                        
                        removeBtn.onclick = (event) => {
                            event.stopPropagation();
                            selectedFiles.splice(index, 1);
                            renderPreviews();
                        };

                        div.appendChild(img);
                        div.appendChild(removeBtn);
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    formData.append('action', 'gh_submit_user_ad');
                    
                    // Replace the files in formData with our selectedFiles array
                    formData.delete('ad_photos[]');
                    selectedFiles.forEach(file => {
                        formData.append('ad_photos[]', file);
                    });
                    
                    if (selectedFiles.length === 0) {
                        message.style.color = 'red';
                        message.textContent = 'Пожалуйста, добавьте хотя бы одно фото.';
                        return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Отправка...';
                    message.textContent = '';

                    fetch(themeData.ajax_url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('successModalAds').classList.add('is-open');
                            form.reset();
                            selectedFiles = [];
                            renderPreviews();
                        } else {
                            message.style.color = 'red';
                            message.textContent = data.data || 'Произошла ошибка при отправке.';
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Отправить на модерацию →';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        message.style.color = 'red';
                        message.textContent = 'Ошибка сети. Попробуйте позже.';
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Отправить на модерацию →';
                    });
            }

            const successModal = document.getElementById('successModalAds');
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
