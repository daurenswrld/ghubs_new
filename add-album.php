<?php 
/* Template Name: Добавить Альбом */
if ( !is_user_logged_in() ) {
    $redirect_url = urlencode(home_url('/add-album/'));
    wp_redirect( home_url('/login/?redirect_to=' . $redirect_url) );
    exit;
}
get_header(); 
?>

    <main class="main-content add-album-page">
        <div class="container">
            <!-- Step 1: Form -->
            <div id="albumStep1" class="step-content active">
                <div class="add-album-header">
                    <h1 class="page-title">Публикация альбома</h1>
                    <p class="page-subtitle">Описание того, что нужно сделать на данном этапе.</p>
                </div>

                <form class="add-album-form" id="addAlbumForm">
                    <div id="albumGlobalError" style="display: none; background: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 77, 77, 0.3);"></div>
                    <!-- Upload Box -->
                    <div class="upload-box" id="uploadArea">
                        <div class="upload-box__icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/upload.svg" alt="">
                        </div>
                        <div class="upload-box__text">
                            <h4>Загрузить фотографии</h4>
                            <p>Перетащите или нажмите на данную область (не более 6 фото, до 2МБ каждое)</p>
                        </div>
                        <input type="file" id="albumFiles" multiple hidden accept="image/*">
                    </div>
                    <div id="imagePreviewContainer" style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px;"></div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <input type="text" class="form-input--pill" id="albumDriveLink" placeholder="Ссылка на диск с фотографиями">
                        </div>
                        
                        <div class="form-group full-width">
                            <input type="text" class="form-input--pill" id="albumTitle" placeholder="Название">
                        </div>

                        <div class="form-row split-2">
                            <div class="form-group custom-select-pill">
                                <select id="albumCountry" required>
                                    <option value="" disabled selected>Загрузка стран...</option>
                                </select>
                                <span class="select-arrow"></span>
                            </div>
                            <div class="form-group icon-input">
                                <input type="text" class="form-input--pill" id="albumCity" list="cityList" placeholder="Город" disabled required>
                                <datalist id="cityList"></datalist>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <input type="text" class="form-input--pill" id="albumLocationName" placeholder="Место проведения (название место)">
                        </div>

                        <div class="form-row split-2">
                            <div class="form-group">
                                <input type="date" class="form-input--pill" id="albumDates" placeholder="Дата">
                            </div>
                            <div class="form-group custom-select-pill">
                                <select id="albumCategory">
                                    <option value="" disabled selected>Категория</option>
                                    <option value="tournaments">Турниры</option>
                                    <option value="camps">Сборы</option>
                                    <option value="seminars">Семинары и мастер классы</option>
                                </select>
                                <span class="select-arrow"></span>
                            </div>
                        </div>

                        <div class="form-row split-2">
                            <div class="form-group icon-input">
                                <input type="tel" class="form-input--pill" id="albumPhone" placeholder="Номер телефона">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </div>
                            <div class="form-group icon-input">
                                <input type="email" class="form-input--pill" id="albumEmail" placeholder="Почта">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                        </div>

                        <div class="form-row split-3">
                            <div class="form-group">
                                <input type="text" class="form-input--pill" id="albumWhatsapp" placeholder="WhatsApp">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-input--pill" id="albumInstagram" placeholder="Instagram">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-input--pill" id="albumTelegram" placeholder="Telegram">
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn--black btn--next" id="btnToStep2">
                            Следующий <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Confirm -->
            <div id="albumStep2" class="step-content">
                <div class="add-album-header">
                    <h1 class="page-title">Подтвердите данные</h1>
                    <p class="page-subtitle">Проверьте правильность заполнения информации перед публикацией.</p>
                </div>

                <div class="confirm-card">
                    <div class="confirm-item">
                        <span class="confirm-label">Название альбома:</span>
                        <span class="confirm-value" id="previewTitle">—</span>
                    </div>
                    <div class="confirm-item">
                        <span class="confirm-label">Локация:</span>
                        <span class="confirm-value" id="previewLocation">—</span>
                    </div>
                    <div class="confirm-item" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                        <span class="confirm-label">Выбранные фото:</span>
                        <div id="previewPhotosList" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:5px;"></div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="button" class="btn btn--outline-dark" id="btnBackToStep1">Назад</button>
                    <button type="button" class="btn btn--black" id="btnConfirmAlbum">Подтвердить и опубликовать</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Success Modal -->
    <div class="global-modal-overlay" id="albumSuccessModal">
        <div class="global-modal">
            <div class="global-modal__icon">✓</div>
            <h3>Альбом отправлен</h3>
            <p style="text-align: center;">Ваш альбом успешно отправлен на модерацию. <br>После проверки он появится в галерее.</p>
            <div class="btn">
                <a href="<?php echo esc_url(home_url('/gallery/')); ?>" class="btn btn--black" style="text-align:center;">Вернуться в галерею</a>
            </div>
        </div>
    </div>

    <style>
        .upload-box {
            margin-bottom: 22px;
        }
        #albumSuccessModal.active {
            display: flex !important;
            opacity: 1 !important;
            pointer-events: all !important;
            z-index: 999999 !important;
        }
        #albumSuccessModal .btn {
            display: block !important;
            width: 100%;
        }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnToStep2 = document.getElementById('btnToStep2');
        const btnBackToStep1 = document.getElementById('btnBackToStep1');
        const btnConfirmAlbum = document.getElementById('btnConfirmAlbum');
        const step1 = document.getElementById('albumStep1');
        const step2 = document.getElementById('albumStep2');
        const previewTitle = document.getElementById('previewTitle');
        const previewLocation = document.getElementById('previewLocation');
        const previewPhotosList = document.getElementById('previewPhotosList');

        const albumGlobalError = document.getElementById('albumGlobalError');
        function showAlbumError(msg) {
            if (!msg) {
                albumGlobalError.style.display = 'none';
                return;
            }
            albumGlobalError.innerText = msg;
            albumGlobalError.style.display = 'block';
            window.scrollTo({ top: albumGlobalError.offsetTop - 50, behavior: 'smooth' });
            setTimeout(() => { albumGlobalError.style.display = 'none'; }, 5000);
        }

        // Dynamic Country and City API

        if(btnToStep2) {
            btnToStep2.addEventListener('click', () => {
                const title = document.getElementById('albumTitle').value.trim();
                const countrySel = document.getElementById('albumCountry');
                const country = countrySel.options[countrySel.selectedIndex]?.text || '';
                const city = document.getElementById('albumCity').value.trim();
                const locationName = document.getElementById('albumLocationName').value.trim();
                
                if (!title) {
                    showAlbumError('Введите название альбома');
                    return;
                }

                previewTitle.textContent = title;
                previewLocation.textContent = `${country}, ${city} | ${locationName}`;
                
                previewPhotosList.innerHTML = '';
                if (processedFiles.length > 0) {
                    processedFiles.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '60px';
                            img.style.height = '60px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '6px';
                            previewPhotosList.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    previewPhotosList.innerHTML = '<span style="color:#888; font-size:14px;">Нет фото</span>';
                }

                step1.classList.remove('active');
                step1.style.display = 'none';
                step2.classList.add('active');
                step2.style.display = 'block';
            });
        }

        if(btnBackToStep1) {
            btnBackToStep1.addEventListener('click', () => {
                step2.classList.remove('active');
                step2.style.display = 'none';
                step1.classList.add('active');
                step1.style.display = 'block';
            });
        }

        // Upload Area and File Processing
        const uploadArea = document.getElementById('uploadArea');
        const albumFiles = document.getElementById('albumFiles');
        const previewContainer = document.getElementById('imagePreviewContainer');
        let processedFiles = []; // Store WebP files

        if(uploadArea && albumFiles) {
            uploadArea.addEventListener('click', (e) => {
                if (e.target !== albumFiles) {
                    albumFiles.click();
                }
            });
            
            // Drag and drop visuals
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = '#1a1a1a';
            });
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.style.borderColor = '#E0E0E0';
            });
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = '#E0E0E0';
                handleFiles(e.dataTransfer.files);
            });

            albumFiles.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });
        }

        async function handleFiles(files) {
            if (!files || files.length === 0) return;
            
            if (processedFiles.length + files.length > 6) {
                showAlbumError('Максимум 6 фотографий!');
                return;
            }

            const maxFileSize = 2 * 1024 * 1024; // 2MB

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.startsWith('image/')) {
                    showAlbumError('Файл ' + file.name + ' не является изображением.');
                    continue;
                }
                if (file.size > maxFileSize) {
                    showAlbumError('Файл ' + file.name + ' превышает 2МБ.');
                    continue;
                }

                try {
                    const webpFile = await convertToWebP(file);
                    processedFiles.push(webpFile);
                    renderPreview(webpFile);
                } catch (e) {
                    console.error('Ошибка конвертации', e);
                }
            }
            
            albumFiles.value = ''; // Reset input
        }

        function convertToWebP(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        const max = 1920;
                        if (width > max || height > max) {
                            if (width > height) {
                                height = Math.round((height * max) / width);
                                width = max;
                            } else {
                                width = Math.round((width * max) / height);
                                height = max;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        canvas.toBlob((blob) => {
                            if (!blob) return reject('Canvas is empty');
                            const newFileName = file.name.replace(/\.[^/.]+$/, "") + ".webp";
                            resolve(new File([blob], newFileName, { type: 'image/webp' }));
                        }, 'image/webp', 0.85);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }

        function renderPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.position = 'relative';
                div.style.width = '80px';
                div.style.height = '80px';
                div.style.borderRadius = '8px';
                div.style.overflow = 'hidden';
                div.style.border = '1px solid #E0E0E0';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                
                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '4px';
                removeBtn.style.right = '4px';
                removeBtn.style.background = 'rgba(0,0,0,0.6)';
                removeBtn.style.color = '#fff';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.width = '20px';
                removeBtn.style.height = '20px';
                removeBtn.style.lineHeight = '20px';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.fontSize = '14px';
                removeBtn.style.display = 'flex';
                removeBtn.style.alignItems = 'center';
                removeBtn.style.justifyContent = 'center';

                removeBtn.onclick = function() {
                    const index = processedFiles.indexOf(file);
                    if (index > -1) {
                        processedFiles.splice(index, 1);
                        div.remove();
                    }
                };

                div.appendChild(img);
                div.appendChild(removeBtn);
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        }

        if(btnConfirmAlbum) {
            btnConfirmAlbum.addEventListener('click', async () => {
                btnConfirmAlbum.disabled = true;
                btnConfirmAlbum.innerText = 'Отправка...';

                const formData = new FormData();
                formData.append('action', 'submit_album');
                formData.append('nonce', typeof themeData !== 'undefined' ? themeData.auth_nonce : '');
                
                const countrySelectForSubmit = document.getElementById('albumCountry');
                const countryText = countrySelectForSubmit.options[countrySelectForSubmit.selectedIndex]?.text || '';
                
                formData.append('title', document.getElementById('albumTitle').value);
                formData.append('drive_link', document.getElementById('albumDriveLink').value);
                formData.append('country', countryText);
                formData.append('city', document.getElementById('albumCity').value);
                formData.append('location_name', document.getElementById('albumLocationName').value);
                formData.append('dates', document.getElementById('albumDates').value);
                formData.append('category', document.getElementById('albumCategory').value);
                formData.append('phone', document.getElementById('albumPhone').value);
                formData.append('email', document.getElementById('albumEmail').value);
                formData.append('whatsapp', document.getElementById('albumWhatsapp').value);
                formData.append('instagram', document.getElementById('albumInstagram').value);
                formData.append('telegram', document.getElementById('albumTelegram').value);

                processedFiles.forEach(file => {
                    formData.append('photos[]', file);
                });

                try {
                    const response = await fetch(typeof themeData !== 'undefined' ? themeData.ajax_url : '/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    });
                    const res = await response.json();
                    if(res.success) {
                        document.getElementById('albumSuccessModal').classList.add('active');
                    } else {
                        showAlbumError(res.data || 'Произошла ошибка при отправке');
                    }
                } catch(err) {
                    console.error(err);
                    showAlbumError('Ошибка сети');
                } finally {
                    btnConfirmAlbum.disabled = false;
                    btnConfirmAlbum.innerText = 'Подтвердить и опубликовать';
                }
            });
        }

        // Optimized Country and City loading (Namaztimes.kz API via Proxy)
        const countrySelect = document.getElementById('albumCountry');
        const cityInput = document.getElementById('albumCity');
        const cityList = document.getElementById('cityList');

        if (countrySelect && cityInput) {
            countrySelect.innerHTML = '<option value="" disabled selected>Загрузка списка стран...</option>';
            
            // Fetch via our server-side proxy to avoid CORS
            const proxyUrl = themeData.ajax_url + '?action=gh_proxy_nt&endpoint=country';
            
            fetch(proxyUrl)
                .then(res => res.json())
                .then(data => {
                    // Normalize data to array
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
                    console.error('Namaztimes API Error:', err);
                    countrySelect.innerHTML = '<option value="" disabled selected>Ошибка загрузки. Попробуйте обновить</option>';
                });

            countrySelect.addEventListener('change', async (e) => {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const countryId = selectedOption ? selectedOption.dataset.id : null;
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

        const successModal = document.getElementById('albumSuccessModal');
        if (successModal) {
            successModal.addEventListener('click', (e) => {
                if (e.target === successModal) {
                    successModal.classList.remove('active');
                }
            });
        }
    });
    </script>

<?php get_footer(); ?>
