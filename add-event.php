<?php
/**
 * Template Name: Добавить мероприятие
 */
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}
get_header(); ?>

    <main>
        <section class="add-event-section">
            <div class="container container--wide">
                <h1 class="add-event-title">Добавление мероприятия</h1>
                
                <div class="stepper">
                    <div class="step active" data-step="1">
                        <span class="step__num">1</span>
                        <span class="step__label">Тип мероприятия</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step__num">2</span>
                        <span class="step__label">Основная информация</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step__num">3</span>
                        <span class="step__label">Проверка и публикация</span>
                    </div>
                </div>

                <p class="step-description" id="stepDescription">Выберите тип вашего мероприятия для продолжения.</p>
                <div id="eventGlobalError" style="display:none; background:rgba(255, 77, 77, 0.15); color:#ff4d4d; padding:15px 20px; border-radius:10px; margin-bottom:20px; font-weight:600; text-align:center; border: 1px solid #ff4d4d;"></div>

                <div class="add-event-content">
                    <form id="addEventFormDynamic" enctype="multipart/form-data">
                        <!-- Step 1: Choice -->
                        <div class="step-content active" id="step1Content">
                            <div class="choice-grid">
                                <?php 
                                $event_types = get_terms(array('taxonomy' => 'event_type', 'hide_empty' => false));
                                foreach ($event_types as $index => $term) : 
                                    $image_id = get_term_meta($term->term_id, 'event_type_image', true);
                                    $image_url = '';
                                    if ($image_id) {
                                        $image_url = wp_get_attachment_image_url($image_id, 'medium');
                                    }
                                ?>
                                <div class="choice-card" data-slug="<?php echo $term->slug; ?>" data-name="<?php echo $term->name; ?>" <?php if ($image_url) echo 'style="background-image: url(\'' . esc_url($image_url) . '\');"'; ?>>
                                    <div class="choice-card__text"><?php echo str_replace(' / ', ' / <br>', $term->name); ?></div>
                                </div>
                                <?php endforeach; ?>
                                <input type="hidden" name="event_type" id="selectedEventType" value="">
                            </div>
                        </div>

                        <!-- Step 2: Information -->
                        <div class="step-content" id="step2Content">
                            <div class="add-event-form">
                                <!-- Photo Upload -->
                                <label class="upload-box upload-row" for="eventPhotos" id="photosLabel">
                                    <div class="upload-box__icon">
                                        <img src="<?php echo get_template_directory_uri(); ?>/img/upload.svg" alt="upload">
                                    </div>
                                    <div class="upload-box__text">
                                        <span class="upload-box__title">Загрузить фотографии</span>
                                        <span class="upload-box__sub">Максимум 10 фото, до 5МБ каждое</span>
                                    </div>
                                    <input type="file" name="event_photos[]" hidden id="eventPhotos" accept="image/*" multiple>
                                </label>
                                <div id="photosPreview" style="display: none; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;"></div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <input type="text" name="title" class="form-input--pill" placeholder="Название мероприятия" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <textarea name="description" class="form-textarea--pill" placeholder="Полное описание мероприятия" required></textarea>
                                    </div>

                                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        <div class="select-wrapper">
                                            <select name="location_country" class="form-input--pill" required id="countrySelect">
                                                <option value="" disabled selected>Загрузка стран...</option>
                                            </select>
                                            <span class="select-arrow"></span>
                                        </div>
                                        <div class="form-group icon-input">
                                            <input type="text" name="city" class="form-input--pill" id="citySelect" list="cityList" placeholder="Город" disabled required>
                                            <datalist id="cityList"></datalist>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="place" class="form-input--pill" placeholder="Место проведения (название стадиона/клуба)" required>
                                    </div>

                                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        <div class="date-wrapper" style="position: relative;">
                                            <input type="date" name="start_date" class="form-input--pill" required>
                                            <span style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #999;">Начало</span>
                                        </div>
                                        <div class="date-wrapper" style="position: relative;">
                                            <input type="date" name="end_date" class="form-input--pill">
                                            <span style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #999;">Конец</span>
                                        </div>
                                    </div>

                                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        <div class="form-group">
                                            <input type="text" name="price" class="form-input--pill" placeholder="Стартовый взнос (если есть)">
                                        </div>
                                        <div class="form-group">
                                            <input type="url" name="reg_url" class="form-input--pill" placeholder="Ссылка на регистрацию">
                                        </div>
                                    </div>

                                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                                        <div class="form-group icon-input">
                                            <input type="text" name="whatsapp" class="form-input--pill" placeholder="WhatsApp (7707...)">
                                        </div>
                                        <div class="form-group icon-input">
                                            <input type="text" name="telegram" class="form-input--pill" placeholder="Telegram (@...)">
                                        </div>
                                        <div class="form-group icon-input">
                                            <input type="text" name="instagram" class="form-input--pill" placeholder="Instagram (@...)">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="organizer" class="form-input--pill" placeholder="Имя организатора / Клуб" required>
                                    </div>

                                    <!-- Document Upload -->
                                    <label class="upload-box upload-row" for="eventDocs" id="docsLabel" style="margin-top: 20px; margin-bottom: 10px;">
                                        <div class="upload-box__icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/img/upload.svg" alt="upload">
                                        </div>
                                        <div class="upload-box__text">
                                            <span class="upload-box__title">Загрузить документы (Положение)</span>
                                            <span class="upload-box__sub">PDF, DOCX до 10МБ</span>
                                        </div>
                                        <input type="file" name="event_docs[]" hidden id="eventDocs" accept=".pdf,.doc,.docx" multiple>
                                    </label>
                                    <div id="docsPreview" style="display: none; margin-bottom: 20px; color: #666; font-size: 14px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Review (Simplified for submission) -->
                        <div class="step-content" id="step3Content">
                            <div class="review-box" style="padding: 40px; background: rgba(255,255,255,1); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; text-align: center;">
                                <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 10px; color: #000;">Почти готово!</h3>
                                <p style="color: rgba(0, 0, 0, 1); font-size: 15px; line-height: 1.5; margin-bottom: 30px;">Пожалуйста, убедитесь, что все данные введены верно. После публикации мероприятие будет отправлено на модерацию.</p>
                                <div id="reviewSummary" style="text-align: left; background: rgba(0,0,0,0.3); padding: 25px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);"></div>
                            </div>
                        </div>

                        <div class="add-event-footer" style="margin-top: 40px; display: flex; gap: 20px; justify-content: center;">
                            <button type="button" class="btn btn--outline-dark" id="btnPrev" style="display: none;">Назад</button>
                            <button type="button" class="btn btn--black" id="btnNext">Продолжить &rarr;</button>
                            <button type="submit" class="btn btn--black" id="btnPublish" style="display: none;">Отправить на модерацию</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Success Modal -->
    <div class="global-modal-overlay" id="successModal">
        <div class="global-modal">
            <div class="global-modal__icon">✓</div>
            <h3>Мероприятие создано!</h3>
            <p>Оно появится в списке после проверки модератором.</p>
            <button class="btn btn--black" onclick="window.location.href='<?php echo home_url('/events/'); ?>'">Перейти к списку</button>
        </div>
    </div>

    <style>
        #successModal.active {
            display: flex !important;
            opacity: 1 !important;
            pointer-events: all !important;
            z-index: 999999 !important;
        }
        #successModal .btn {
            display: block !important;
            width: 100%;
        }
    </style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addEventFormDynamic');
    const steps = document.querySelectorAll('.step');
    const contents = document.querySelectorAll('.step-content');
    const btnNext = document.getElementById('btnNext');
    const btnPrev = document.getElementById('btnPrev');
    const btnPublish = document.getElementById('btnPublish');
    const choiceCards = document.querySelectorAll('.choice-card');
    const selectedTypeInput = document.getElementById('selectedEventType');
    const eventGlobalError = document.getElementById('eventGlobalError');
    
    let currentStep = 1;

    function showEventError(msg) {
        if (!msg) {
            eventGlobalError.style.display = 'none';
            return;
        }
        eventGlobalError.innerText = msg;
        eventGlobalError.style.display = 'block';
        window.scrollTo({ top: eventGlobalError.offsetTop - 50, behavior: 'smooth' });
        setTimeout(() => { eventGlobalError.style.display = 'none'; }, 4000);
    }

    function updateSteps() {
        steps.forEach(s => s.classList.toggle('active', s.dataset.step == currentStep));
        contents.forEach((c, idx) => c.classList.toggle('active', idx + 1 == currentStep));
        
        btnPrev.style.display = currentStep > 1 ? 'block' : 'none';
        btnNext.style.display = currentStep < 3 ? 'block' : 'none';
        btnPublish.style.display = currentStep == 3 ? 'block' : 'none';

        if (currentStep === 3) {
            const formData = new FormData(form);
            const cityName = document.getElementById('citySelect').value;
            
            const formatDate = (dateStr) => {
                if (!dateStr) return 'Не указано';
                const d = new Date(dateStr);
                return d.toLocaleDateString('ru-RU', {day: 'numeric', month: 'long', year: 'numeric'});
            };

            let html = `
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                    <span style="color: rgba(0,0,0,0.5); font-size: 14px; font-weight: 500;">Название</span>
                    <strong style="color: #000; font-size: 16px; font-weight: 600; text-align: right; max-width: 60%;">${formData.get('title')}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                    <span style="color: rgba(0,0,0,0.5); font-size: 14px; font-weight: 500;">Тип</span>
                    <strong style="color: #000; font-size: 16px; font-weight: 600; text-align: right; max-width: 60%;">${document.querySelector('.choice-card.active').dataset.name}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                    <span style="color: rgba(0,0,0,0.5); font-size: 14px; font-weight: 500;">Локация</span>
                    <strong style="color: #000; font-size: 16px; font-weight: 600; text-align: right; max-width: 60%;">${cityName}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                    <span style="color: rgba(0,0,0,0.5); font-size: 14px; font-weight: 500;">Контакты</span>
                    <strong style="color: #000; font-size: 16px; font-weight: 600; text-align: right; max-width: 60%;">
                        ${formData.get('whatsapp') ? 'WA: ' + formData.get('whatsapp') + '<br>' : ''} 
                        ${formData.get('telegram') ? 'TG: ' + formData.get('telegram') + '<br>' : ''}
                        ${formData.get('instagram') ? 'Inst: ' + formData.get('instagram') : ''}
                        ${(!formData.get('whatsapp') && !formData.get('telegram') && !formData.get('instagram')) ? 'Не указаны' : ''}
                    </strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                    <span style="color: rgba(0,0,0,0.5); font-size: 14px; font-weight: 500;">Начало</span>
                    <strong style="color: #000; font-size: 16px; font-weight: 600; text-align: right; max-width: 60%;">${formatDate(formData.get('start_date'))}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px;">
                    <span style="color: rgba(0,0,0,0.5); font-size: 14px; font-weight: 500;">Документы</span>
                    <strong style="color: #000; font-size: 14px; font-weight: 600; text-align: right; max-width: 60%;">
                        ${document.getElementById('eventDocs').files.length > 0 
                            ? Array.from(document.getElementById('eventDocs').files).map(f => f.name).join(', ') 
                            : 'Не загружены'}
                    </strong>
                </div>
            `;
            document.getElementById('reviewSummary').innerHTML = html;
        }
    }

    choiceCards.forEach(card => {
        card.addEventListener('click', () => {
            choiceCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            selectedTypeInput.value = card.dataset.slug;
        });
    });

    btnNext.addEventListener('click', () => {
        if (currentStep === 1) {
            if (!document.querySelector('.choice-card.active')) {
                showEventError('Пожалуйста, выберите тип мероприятия для продолжения.');
                return;
            }
        }
        
        if (currentStep === 2) {
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
        }

        if (currentStep < 3) {
            showEventError(''); // Clear error if moving forward
            currentStep++;
            updateSteps();
        }
    });

    btnPrev.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        btnPublish.disabled = true;
        btnPublish.textContent = 'Отправка...';

        const formData = new FormData(form);
        formData.append('action', 'gh_submit_event');

        fetch(themeData.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('successModal').classList.add('active');
            } else {
                alert(data.data);
                btnPublish.disabled = false;
                btnPublish.textContent = 'Отправить на модерацию';
            }
        });
    });

    // Unified File Preview with Delete support
    function setupFilePreview(inputId, previewId, isImage = true) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        let filesArray = [];

        input.addEventListener('change', () => {
            // Append new files to existing ones (or replace, depending on preference)
            // Here we replace to keep it simple and consistent with browser behavior
            filesArray = Array.from(input.files);
            render();
        });

        function render() {
            preview.innerHTML = '';
            if (filesArray.length > 0) {
                preview.style.display = isImage ? 'flex' : 'block';
                if (!isImage) {
                    preview.style.background = '#F8F9FA';
                    preview.style.padding = '15px';
                    preview.style.borderRadius = '12px';
                    preview.style.marginTop = '10px';
                    preview.style.border = '1px dashed #E0E0E0';
                }
                filesArray.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    item.style.position = 'relative';
                    item.style.display = isImage ? 'inline-block' : 'flex';
                    if (isImage) {
                        item.style.margin = '0 10px 10px 0';
                    } else {
                        item.style.alignItems = 'center';
                        item.style.gap = '10px';
                        item.style.marginBottom = '8px';
                        item.style.background = '#f3f4f6';
                        item.style.padding = '8px 12px';
                        item.style.borderRadius = '8px';
                    }

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-btn';
                    removeBtn.style.cssText = `
                        background: #ff4d4d; color: #fff; border: none; 
                        width: 20px; height: 20px; border-radius: 50%; 
                        cursor: pointer; display: flex; align-items: center; 
                        justify-content: center; font-size: 14px; z-index: 10;
                        transition: transform 0.2s;
                    `;
                    
                    if (isImage) {
                        removeBtn.style.position = 'absolute';
                        removeBtn.style.top = '-8px';
                        removeBtn.style.right = '-8px';
                    } else {
                        removeBtn.style.marginLeft = 'auto';
                    }

                    removeBtn.onmouseover = () => removeBtn.style.transform = 'scale(1.1)';
                    removeBtn.onmouseout = () => removeBtn.style.transform = 'scale(1)';

                    removeBtn.onclick = (e) => {
                        e.preventDefault();
                        filesArray.splice(index, 1);
                        
                        // Sync with input
                        const dt = new DataTransfer();
                        filesArray.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                        
                        render();
                    };

                    if (isImage) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '80px';
                            img.style.height = '80px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '10px';
                            img.style.border = '1px solid #ddd';
                            item.appendChild(img);
                            item.appendChild(removeBtn);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        const span = document.createElement('span');
                        span.textContent = '📄 ' + file.name;
                        span.style.fontSize = '14px';
                        item.appendChild(span);
                        item.appendChild(removeBtn);
                    }
                    preview.appendChild(item);
                });
            } else {
                preview.style.display = 'none';
            }
        }
    }

    setupFilePreview('eventPhotos', 'photosPreview', true);
    setupFilePreview('eventDocs', 'docsPreview', false);

    // Dynamic Country and City via Namaztimes API
    const countrySelect = document.getElementById('countrySelect');
    const cityInput = document.getElementById('citySelect');
    const cityList = document.getElementById('cityList');

    if (countrySelect && cityInput) {
        countrySelect.innerHTML = '<option value="" disabled selected>Загрузка списка стран...</option>';
        
        const proxyUrl = themeData.ajax_url + '?action=gh_proxy_nt&endpoint=country';
        
        fetch(proxyUrl)
            .then(res => res.json())
            .then(data => {
                let countries = [];
                if (Array.isArray(data)) {
                    countries = data;
                } else if (typeof data === 'object' && data !== null) {
                    countries = Object.entries(data).map(([id, val]) => {
                        return (typeof val === 'object') ? { ...val, id } : { ru: val, id };
                    });
                }

                if (countries.length === 0) throw new Error('No data');
                
                countrySelect.innerHTML = '<option value="" disabled selected>Выберите страну</option>';
                
                const addOption = (c) => {
                    const opt = new Option(c.ru, c.ru);
                    opt.dataset.id = c.id;
                    countrySelect.add(opt);
                };

                const kz = countries.find(c => String(c.id) === "99");
                const ru = countries.find(c => String(c.id) === "100");
                
                if (kz) addOption(kz);
                if (ru) addOption(ru);

                const others = countries.filter(c => String(c.id) !== "99" && String(c.id) !== "100")
                                       .sort((a, b) => (a.ru || '').localeCompare(b.ru || '', 'ru'));

                others.forEach(c => {
                    if (c.ru) addOption(c);
                });
                
                countrySelect.add(new Option("Другая страна", "Другая страна"));
            })
            .catch(err => {
                console.error('API Error:', err);
                countrySelect.innerHTML = '<option value="" disabled selected>Ошибка загрузки. Попробуйте обновить</option>';
            });

        countrySelect.addEventListener('change', async (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const countryId = selectedOption.dataset.id;
            
            cityInput.value = '';
            cityList.innerHTML = '';
            
            if (!countryId || e.target.value === "Другая страна") {
                cityInput.disabled = false;
                cityInput.placeholder = "Введите город";
                return;
            }

            cityInput.disabled = true;
            cityInput.placeholder = 'Загрузка городов...';

            try {
                const cityProxyUrl = themeData.ajax_url + `?action=gh_proxy_nt&endpoint=city&id=${countryId}`;
                const response = await fetch(cityProxyUrl);
                const data = await response.json();
                
                if (Array.isArray(data)) {
                    const uniqueCities = data.map(c => c.ru).sort((a, b) => a.localeCompare(b, 'ru'));
                    uniqueCities.forEach(cityName => {
                        const opt = document.createElement('option');
                        opt.value = cityName;
                        cityList.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error('City Load Error:', err);
            } finally {
                cityInput.disabled = false;
                cityInput.placeholder = 'Город (выберите или введите)';
            }
        });
    }
});
</script>

<?php get_footer(); ?>
