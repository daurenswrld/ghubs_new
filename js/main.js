// Gymnastics Hub - Main Script (Hero Block Update)



document.addEventListener('DOMContentLoaded', () => {
    // -- Lenis Smooth Scroll Initialization --
    let lenis;
    if (typeof Lenis !== 'undefined') {
        lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            wheelMultiplier: 1,
            touchMultiplier: 2,
            infinite: false,
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    }

    // Preloader Logic (Restored Animation)
    const preloader = document.getElementById('preloader');
    if (preloader) {
        // Check if preloader has already been shown in this session
        if (!sessionStorage.getItem('gh_preloader_shown')) {
            if (lenis) lenis.stop();
            
            const minDisplayTime = 1600; // Time for animation to play
            const startTime = Date.now();

            const hidePreloader = () => {
                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minDisplayTime - elapsedTime);

                setTimeout(() => {
                    preloader.classList.add('preloader--hidden');
                    document.body.classList.remove('loading');
                    if (lenis) lenis.start();
                    setTimeout(() => { preloader.style.display = 'none'; }, 800);
                }, remainingTime);
            };

            // Safety timeout: hide anyway after 3 seconds
            setTimeout(hidePreloader, 3000);

            if (document.readyState === 'complete') {
                hidePreloader();
            } else {
                window.addEventListener('load', hidePreloader);
            }

            // Mark preloader as shown for this session
            sessionStorage.setItem('gh_preloader_shown', 'true');
        } else {
            // Already shown in this session, hide immediately
            preloader.style.display = 'none';
            document.body.classList.remove('loading');
            if (lenis) lenis.start();
        }
    } else {
        document.body.classList.remove('loading');
        if (lenis) lenis.start();
    }
    // Tab switching for Search Card
    const tabButtons = document.querySelectorAll('.tab-btn');
    
    if (tabButtons.length > 0) {
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Here you can add logic to filter results or change inputs
                const tabType = button.getAttribute('data-tab');
                console.log(`Switched to tab: ${tabType}`);
            });
        });
    }

    // Dropdown toggle logic (for mobile/hover)
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('mouseenter', () => {
            // Logic for showing dropdown menu on desktop
        });
        
        dropdown.addEventListener('mouseleave', () => {
            // Logic for hiding dropdown menu on desktop
        });
    });

    // Mobile Menu Toggle — handled in the Burger section below


     // Promo Slider Logic
    const sliderTrack = document.querySelector('.promo-slider__track');
    const slides = document.querySelectorAll('.promo-slide');

    if (sliderTrack && slides.length > 0) {
        // Helper to center slide smoothly (fixed to prevent window jumping)
        const centerActiveSlide = (slide, smooth = true) => {
            if (!slide || !sliderTrack) return;
            
            const container = sliderTrack.parentElement; // .promo-slider
            const scrollLeft = slide.offsetLeft - (container.clientWidth / 2) + (slide.clientWidth / 2);
            
            container.scrollTo({
                left: scrollLeft,
                behavior: smooth ? 'smooth' : 'auto'
            });
        };

        let currentIndex = (slides.length > 1) ? 1 : 0;
        let direction = 1; // 1 for forward, -1 for backward
        let autoScrollInterval = null;

        const startAutoScroll = () => {
            if (slides.length <= 1) return;
            clearInterval(autoScrollInterval);
            autoScrollInterval = setInterval(() => {
                let nextIndex = currentIndex + direction;
                
                // If we reach the end, reverse direction
                if (nextIndex >= slides.length) {
                    direction = -1;
                    nextIndex = slides.length - 2;
                } else if (nextIndex < 0) {
                    direction = 1;
                    nextIndex = 1;
                }

                if (nextIndex >= 0 && nextIndex < slides.length) {
                    currentIndex = nextIndex;
                    const nextSlide = slides[currentIndex];
                    
                    slides.forEach(s => s.classList.remove('active'));
                    nextSlide.classList.add('active');
                    centerActiveSlide(nextSlide);
                }
            }, 5000);
        };

        const stopAutoScroll = () => {
            clearInterval(autoScrollInterval);
        };

        // Pause auto-scroll on hover
        const sliderContainer = sliderTrack.parentElement;
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', stopAutoScroll);
            sliderContainer.addEventListener('mouseleave', startAutoScroll);
        }

        slides.forEach((slide, index) => {
            slide.addEventListener('click', (e) => {
                const isActive = slide.classList.contains('active');
                const href = slide.getAttribute('href');

                if (isActive && href && href !== '#') {
                    // If already active and has a real link, allow navigation
                    return;
                }

                // Otherwise, prevent navigation and just activate/center
                e.preventDefault();
                slides.forEach(s => s.classList.remove('active'));
                slide.classList.add('active');
                centerActiveSlide(slide);

                // Update auto-scroll state
                currentIndex = index;
                if (currentIndex === slides.length - 1) {
                    direction = -1;
                } else if (currentIndex === 0) {
                    direction = 1;
                }

                // Reset interval timer
                startAutoScroll();
            });
        });

        // Initial state selection: prefer 2nd slide
        let initialSlide = slides[0];
        if (slides.length > 1) {
            initialSlide = slides[1];
        }

        // Reset and set active
        slides.forEach(s => s.classList.remove('active'));
        
        // Final centering with delay to ensure rendering is complete
        setTimeout(() => {
            initialSlide.classList.add('active');
            centerActiveSlide(initialSlide);
            startAutoScroll();
        }, 500);
    }



    // Catalog Filters Logic (Tag Selection)
    const filterTags = document.querySelectorAll('.filter-tag');
    if (filterTags.length > 0) {
        filterTags.forEach(tag => {
            tag.addEventListener('click', () => {
                filterTags.forEach(t => t.classList.remove('active'));
                tag.classList.add('active');
                
                // For demonstration: logic for filtering could be added here
                console.log(`Filtering catalog by: ${tag.textContent.trim()}`);
            });
        });
    }

    // Catalog Date Picker Logic
    const dateInput = document.getElementById('dateInput');
    const dateValue = document.getElementById('dateValue');
    const dateContainer = document.getElementById('datePickerContainer');

    if (dateInput && dateContainer) {
        // Open date picker when the whole box is clicked
        dateContainer.addEventListener('click', () => {
            try {
                if ('showPicker' in HTMLInputElement.prototype) {
                    dateInput.showPicker();
                } else {
                    dateInput.click();
                }
            } catch (err) {
                dateInput.click();
            }
        });

        // Update display text when date is chosen
        dateInput.addEventListener('change', (e) => {
            const selectedDate = e.target.value;
            if (selectedDate && dateValue) {
                const dateParts = selectedDate.split('-');
                const formattedDate = `${dateParts[2]}.${dateParts[1]}.${dateParts[0]}`;
                dateValue.innerText = formattedDate;
                dateValue.style.color = '#1a1a1a';
                dateValue.style.opacity = '1';
            }
        });
    }

    // Event Gallery Slider Logic
    const galleryPrev = document.getElementById('galleryPrev');
    const galleryNext = document.getElementById('galleryNext');
    const galleryDots = document.querySelectorAll('.gallery-dots .dot');
    const mainImg = document.getElementById('mainGalleryImg');
    
    // Test images array (using existing project images for now)
    const galleryImages = [
        (window.themeData?.templateUri || '') + '/img/card.png',
        (window.themeData?.templateUri || '') + '/img/test-banner.webp',
        (window.themeData?.templateUri || '') + '/img/card.png', 
        (window.themeData?.templateUri || '') + '/img/test-banner.webp'
    ];
    
    if (galleryPrev && galleryNext && galleryDots.length > 0 && mainImg) {
        let currentIdx = 0;
        
        const updateGallery = (newIdx) => {
            currentIdx = newIdx;
            // Update dots
            galleryDots.forEach(dot => dot.classList.remove('active'));
            galleryDots[currentIdx].classList.add('active');
            
            // Fixed: Real image switching with fade effect
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = galleryImages[currentIdx % galleryImages.length];
                mainImg.style.opacity = '1';
            }, 200);
        };

        galleryPrev.addEventListener('click', () => {
            let nextIdx = (currentIdx > 0) ? currentIdx - 1 : galleryDots.length - 1;
            updateGallery(nextIdx);
        });

        galleryNext.addEventListener('click', () => {
            let nextIdx = (currentIdx < galleryDots.length - 1) ? currentIdx + 1 : 0;
            updateGallery(nextIdx);
        });

        galleryDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                updateGallery(index);
            });
        });
    }

    // Lightbox Logic
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxClose = document.getElementById('lightboxClose');

    if (lightbox && mainImg && lightboxImg) {
        // Open lightbox
        mainImg.addEventListener('click', () => {
            lightboxImg.src = mainImg.src;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden'; // Stop scrolling
        });

        // Close lightbox
        const closeLightbox = () => {
            lightbox.classList.remove('active');
            document.body.style.overflow = 'auto'; // Restore scrolling
        };

        if (lightboxClose) {
            lightboxClose.addEventListener('click', closeLightbox);
        }

        // Close on background click
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                closeLightbox();
            }
        });
    }


    // Role Selector Logic
    const roleOptions = document.querySelectorAll('.role-option');
    const selectedRoleInput = document.getElementById('selectedRole');

    if (roleOptions.length > 0 && selectedRoleInput) {
        const regDescGroup = document.getElementById('regDescGroup');
        roleOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Remove active from others
                roleOptions.forEach(opt => opt.classList.remove('active'));
                // Add active to clicked
                option.classList.add('active');
                // Update hidden input
                const selectedRole = option.getAttribute('data-role');
                selectedRoleInput.value = selectedRole;
                
                if (regDescGroup) {
                    if (selectedRole === 'gh_club' || selectedRole === 'gh_organizer') {
                        regDescGroup.style.display = 'block';
                    } else {
                        regDescGroup.style.display = 'none';
                        const textarea = regDescGroup.querySelector('textarea');
                        if (textarea) textarea.value = '';
                    }
                }
                
                console.log(`Selected role: ${selectedRole}`);
            });
        });
    }

    // --- Password Toggle Logic ---
    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('.password-toggle');
        if (toggle) {
            e.preventDefault();
            const group = toggle.closest('.password-group');
            if (!group) return;

            const input = group.querySelector('input');
            const eyeOpen = toggle.querySelector('.eye-open');
            const eyeClosed = toggle.querySelector('.eye-closed');

            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeOpen) eyeOpen.style.display = 'none';
                    if (eyeClosed) eyeClosed.style.display = 'block';
                } else {
                    input.type = 'password';
                    if (eyeOpen) eyeOpen.style.display = 'block';
                    if (eyeClosed) eyeClosed.style.display = 'none';
                }
            }
        }
    });


    // --- SPA Auth Logic ---
    const authCard = document.querySelector('.auth-card');
    const authTitle = document.querySelector('.auth-card__title');
    const authSubtext = document.querySelector('.auth-card__subtext');
    const authForms = document.querySelectorAll('.auth-form');
    const recoverySuccess = document.querySelector('.recovery-success');

    if (authCard && authTitle) {
        window.showAuthState = function(state, extra = '') {
            // Hide all states first
            authForms.forEach(f => f.classList.remove('active'));
            if (recoverySuccess) recoverySuccess.style.display = 'none';
            if (authSubtext) authSubtext.style.display = 'none';
            authTitle.style.display = 'block';

            // Switch logic
            switch(state) {
                case 'login':
                    authTitle.innerText = 'Вход в аккаунт';
                    document.getElementById('loginForm').classList.add('active');
                    break;
                case 'register':
                    authTitle.innerText = 'Создать аккаунт';
                    document.getElementById('registerForm').classList.add('active');
                    break;
                case 'recovery':
                    authTitle.innerText = 'Восстановление аккаунта';
                    document.getElementById('recoveryForm').classList.add('active');
                    break;
                case 'reset':
                    authTitle.innerText = 'Восстановление аккаунта';
                    if (authSubtext) {
                        authSubtext.innerText = 'Введите код и новый пароль';
                        authSubtext.style.display = 'block';
                    }
                    document.getElementById('resetForm').classList.add('active');
                    break;
                case 'success':
                    authTitle.style.display = 'none';
                    if (recoverySuccess) recoverySuccess.style.display = 'block';
                    break;
            }
            
            // Scroll to top of card for better UX
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        // Check hash on load to show specific state (e.g. login.html#reset)
        const initialHash = window.location.hash.substring(1);
        if (initialHash && ['login', 'register', 'recovery', 'reset', 'success'].includes(initialHash)) {
            showAuthState(initialHash);
        }

        // Attach listeners to navigation links
        document.body.addEventListener('click', (e) => {
            const target = e.target.closest('a');
            if (!target) return;

            const href = target.getAttribute('href');
            if (!href) return;
            
            // Check if we are on the auth page or at least have the forms
            if (!document.getElementById('loginForm')) return;

            if (href.endsWith('register.html')) {
                e.preventDefault();
                showAuthState('register');
            } else if (href.endsWith('login.html')) {
                e.preventDefault();
                showAuthState('login');
            } else if (href.endsWith('forgot-password.html')) {
                e.preventDefault();
                showAuthState('recovery');
            }
        });

        // --- ROBUST AJAX AUTH LOGIC ---
        console.log('Auth Logic Initializing...');

        const showMessage = (form, message, isError = true) => {
            let msgContainer = form.querySelector('.auth-message');
            if (!msgContainer) {
                msgContainer = document.createElement('div');
                msgContainer.className = 'auth-message';
                form.insertBefore(msgContainer, form.querySelector('.auth-form__actions'));
            }
            
            msgContainer.innerText = message;
            msgContainer.style.display = 'block';
            msgContainer.style.padding = '12px';
            msgContainer.style.borderRadius = '10px';
            msgContainer.style.marginBottom = '20px';
            msgContainer.style.textAlign = 'center';
            msgContainer.style.fontSize = '14px';
            msgContainer.style.fontWeight = '600';
            msgContainer.style.transition = 'all 0.3s ease';
            
            if (isError) {
                msgContainer.style.background = 'rgba(255, 77, 77, 0.15)';
                msgContainer.style.color = '#ff4d4d';
                msgContainer.style.border = '1px solid #ff4d4d';
                
                // Shake animation
                form.style.animation = 'none';
                setTimeout(() => {
                    form.style.animation = 'shake 0.4s ease-in-out';
                }, 10);
            } else {
                msgContainer.style.background = 'rgba(46, 204, 113, 0.15)';
                msgContainer.style.color = '#2ecc71';
                msgContainer.style.border = '1px solid #2ecc71';
            }
        };

        // Add shake animation style if not exists
        if (!document.getElementById('gh-auth-styles')) {
            const style = document.createElement('style');
            style.id = 'gh-auth-styles';
            style.innerHTML = `
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-8px); }
                    50% { transform: translateX(8px); }
                    75% { transform: translateX(-4px); }
                }
            `;
            document.head.appendChild(style);
        }

        const handleAuthSubmit = (formId, action) => {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const formData = new FormData(this);
                
                // Password Validation for Registration and Reset
                if (action === 'gh_ajax_register' || action === 'gh_ajax_reset_password') {
                    const pass = formData.get(action === 'gh_ajax_register' ? 'password' : 'new_password');
                    const passRegex = /^(?=.*[A-Z]).{6,}$/;
                    if (!passRegex.test(pass)) {
                        showMessage(this, 'Пароль должен быть от 6 символов и содержать заглавную букву.');
                        return false;
                    }
                    if (action === 'gh_ajax_register') {
                        const confirm = formData.get('password_confirm');
                        if (pass !== confirm) {
                            showMessage(this, 'Пароли не совпадают!');
                            return false;
                        }
                    }
                }

                formData.append('action', action);
                formData.append('nonce', themeData.auth_nonce);

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerText;
                btn.disabled = true;
                btn.innerText = 'Подождите...';

                fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (action === 'gh_ajax_recovery') {
                            // Switch to Reset Form
                            document.getElementById('resetEmail').value = formData.get('email');
                            showAuthState('reset');
                        } else if (data.data.redirect) {
                            showMessage(this, data.data.message, false);
                            setTimeout(() => {
                                window.location.href = data.data.redirect;
                            }, 1000);
                        } else {
                            showMessage(this, data.data.message, false);
                        }
                    } else {
                        showMessage(this, data.data.message || 'Произошла ошибка');
                    }
                    btn.disabled = false;
                    btn.innerText = originalText;
                })
                .catch(err => {
                    showMessage(this, 'Ошибка соединения с сервером.');
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
            });
        };

        handleAuthSubmit('loginForm', 'gh_ajax_login');
        handleAuthSubmit('registerForm', 'gh_ajax_register');
        handleAuthSubmit('recoveryForm', 'gh_ajax_recovery');
        handleAuthSubmit('resetForm', 'gh_ajax_reset_password');
        handleAuthSubmit('verifyEmailForm', 'gh_ajax_verify_email');

        // Handle Resend Code
        const resendBtn = document.getElementById('resendCode');
        if (resendBtn) {
            resendBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('verifyEmailForm');
                const email = form.querySelector('[name="email"]').value;
                
                resendBtn.innerText = 'Отправка...';
                resendBtn.style.pointerEvents = 'none';

                const formData = new FormData();
                formData.append('action', 'gh_ajax_resend_code');
                formData.append('nonce', themeData.auth_nonce);
                formData.append('email', email);

                fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showMessage(form, data.data.message, false);
                        // Update debug code on page if it exists
                        const debugSpan = document.querySelector('.verify-subtext b');
                        if (debugSpan && data.data.debug_code) {
                            debugSpan.innerText = data.data.debug_code;
                        }
                    } else {
                        showMessage(form, data.data.message);
                    }
                    resendBtn.innerText = 'Отправить код повторно';
                    resendBtn.style.pointerEvents = 'auto';
                })
                .catch(() => {
                    showMessage(form, 'Ошибка при повторной отправке.');
                    resendBtn.innerText = 'Отправить код повторно';
                    resendBtn.style.pointerEvents = 'auto';
                });
            });
        }
    }



    // ── Burger / Mobile Navigation ──────────────────────────────────────
    const header     = document.querySelector('.main-header');
    const mobileNav  = document.querySelector('.mobile-nav');
    const menuToggle = document.querySelector('.menu-toggle');

    if (menuToggle && mobileNav && header) {
        // Open / close
        const openMenu = () => {
            header.classList.add('nav-open');
            mobileNav.classList.add('is-open');
            document.body.style.overflow = 'hidden'; // lock scroll
        };

        const closeMenu = () => {
            header.classList.remove('nav-open');
            mobileNav.classList.remove('is-open');
            document.body.style.overflow = '';
        };

        menuToggle.addEventListener('click', () => {
            if (mobileNav.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close via Close button
        const closeBtn = mobileNav.querySelector('.mobile-nav__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeMenu);
        }

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMenu();
        });

        // Close when a plain link (not dropdown toggle) is clicked
        mobileNav.querySelectorAll('a:not(.has-dropdown)').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Accordion dropdowns inside mobile nav
        mobileNav.querySelectorAll('.mobile-nav__link.has-dropdown').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const dropdown = toggle.nextElementSibling;
                const isOpen = toggle.classList.contains('open');

                // Close all others first
                mobileNav.querySelectorAll('.mobile-nav__link.has-dropdown.open').forEach(el => {
                    el.classList.remove('open');
                    el.nextElementSibling?.classList.remove('is-open');
                });

                if (!isOpen) {
                    toggle.classList.add('open');
                    dropdown?.classList.add('is-open');
                }
            });
        });
    }


    // -- Ad Placement Modal Logic --
    const adModal = document.getElementById('adModal');
    const adCtaBtn = document.querySelector('.ad-cta');
    const closeAdModalBtn = document.getElementById('closeAdModal');
    const adRequestForm = document.getElementById('adRequestForm');
    const adUploadBoxV2 = document.getElementById('adUploadBoxV2');
    const adFileInputV2 = document.getElementById('adFileInputV2');
    const adSelectV2 = document.getElementById('adSelectV2');

    const openAdModal = (e) => {
        if (e) e.preventDefault();
        if (adModal) {
            adModal.querySelector('.ad-modal__form').style.display = 'block';
            adModal.querySelector('.ad-modal__success').style.display = 'none';
            adModal.classList.add('is-open');
            if (typeof lenis !== 'undefined') lenis.stop();
        }
    };

    const closeAdModal = () => {
        if (adModal) {
            adModal.classList.remove('is-open');
            if (typeof lenis !== 'undefined') lenis.start();
        }
    };

    if (adCtaBtn) adCtaBtn.addEventListener('click', openAdModal);
    if (closeAdModalBtn) closeAdModalBtn.addEventListener('click', closeAdModal);

    // Custom Select V2 Logic
    if (adSelectV2) {
        const selected = adSelectV2.querySelector('.select-selected-v2');
        const items = adSelectV2.querySelector('.select-items-v2');
        const hiddenInput = document.getElementById('ad_type_hidden');

        selected.addEventListener('click', (e) => {
            e.stopPropagation();
            adSelectV2.classList.toggle('active');
            items.classList.toggle('select-hide-v2');
        });

        items.querySelectorAll('div').forEach(item => {
            item.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                const text = this.querySelector('span:first-child').innerText;
                selected.querySelector('span:first-child').innerText = text;
                hiddenInput.value = val;
                items.classList.add('select-hide-v2');
                adSelectV2.classList.remove('active');
            });
        });

        document.addEventListener('click', () => {
            items.classList.add('select-hide-v2');
            adSelectV2.classList.remove('active');
        });
    }

    if (adUploadBoxV2 && adFileInputV2) {
        adUploadBoxV2.addEventListener('click', () => adFileInputV2.click());
        adFileInputV2.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const uploadText = adUploadBoxV2.querySelector('p');
                if (uploadText) uploadText.innerText = `Выбран файл: ${fileName}`;
                adUploadBoxV2.style.borderColor = '#000';
            }
        });
    }

    if (adRequestForm) {
        adRequestForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = adRequestForm.querySelector('.btn-submit-v2');
            const originalBtnText = submitBtn.innerHTML;
            
            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Отправка...';
                
                const formData = new FormData(adRequestForm);
                formData.append('action', 'submit_ad_request');
                
                const response = await fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                
                const res = await response.json();
                
                if (res.success) {
                    // Switch states
                    adModal.querySelector('.ad-modal__form').style.display = 'none';
                    adModal.querySelector('.ad-modal__success').style.display = 'block';
                    adRequestForm.reset();
                } else {
                    alert(res.data.message || 'Ошибка при отправке');
                }
            } catch (err) {
                console.error('Ad request error:', err);
                alert('Произошла ошибка. Попробуйте позже.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }

    const closeSuccessBtnV2 = document.getElementById('closeSuccessBtnV2');
    if (closeSuccessBtnV2) {
        closeSuccessBtnV2.addEventListener('click', closeAdModal);
    }

    if (adModal) {
        adModal.addEventListener('click', (e) => {
            if (e.target === adModal) closeAdModal();
        });
    }

    // --- Forum Voting Logic ---
    document.body.addEventListener('click', async (e) => {
        const voteBtn = e.target.closest('.vote-btn, .vote-comment-btn');
        if (!voteBtn) return;

        e.preventDefault();
        
        const id = voteBtn.getAttribute('data-id');
        const type = voteBtn.getAttribute('data-type');
        const isComment = voteBtn.classList.contains('vote-comment-btn');
        const countSpan = voteBtn.querySelector('.count');

        const formData = new FormData();
        formData.append('action', 'forum_vote');
        formData.append('id', id);
        formData.append('type', type);
        formData.append('vote_for', isComment ? 'comment' : 'topic');

        try {
            voteBtn.style.opacity = '0.5';
            voteBtn.style.pointerEvents = 'none';

            const response = await fetch(themeData.ajax_url, {
                method: 'POST',
                body: formData
            });
            const res = await response.json();
            
            if (res.success) {
                // Find sibling buttons to update both counts
                const actionGroup = voteBtn.closest('.action-group');
                if (actionGroup) {
                    const likeBtn = actionGroup.querySelector('[data-type="like"]');
                    const dislikeBtn = actionGroup.querySelector('[data-type="dislike"]');
                    
                    if (likeBtn) {
                        const likeCount = likeBtn.querySelector('.count');
                        if (likeCount) likeCount.textContent = res.data.likes;
                        if (res.data.action === 'voted' && type === 'like') likeBtn.classList.add('voted');
                        else if (res.data.action === 'unvoted' && type === 'like') likeBtn.classList.remove('voted');
                    }
                    
                    if (dislikeBtn) {
                        const dislikeCount = dislikeBtn.querySelector('.count');
                        if (dislikeCount) dislikeCount.textContent = res.data.dislikes;
                        if (res.data.action === 'voted' && type === 'dislike') dislikeBtn.classList.add('voted');
                        else if (res.data.action === 'unvoted' && type === 'dislike') dislikeBtn.classList.remove('voted');
                    }

                    // Special case: if switching, remove voted class from sibling
                    if (res.data.action === 'voted') {
                        if (type === 'like' && dislikeBtn) dislikeBtn.classList.remove('voted');
                        if (type === 'dislike' && likeBtn) likeBtn.classList.remove('voted');
                    }
                }
            }
        } catch (err) {
            console.error('Vote error:', err);
        } finally {
            voteBtn.style.opacity = '1';
            voteBtn.style.pointerEvents = 'auto';
        }
    });

    // --- Forum Reply Scroll Logic ---
    const replyMainBtn = document.getElementById('replyMainBtn');
    if (replyMainBtn) {
        replyMainBtn.addEventListener('click', () => {
            const commentForm = document.getElementById('commentform');
            if (commentForm) {
                commentForm.scrollIntoView({ behavior: 'smooth' });
                const textarea = commentForm.querySelector('textarea');
                if (textarea) textarea.focus();
            }
        });
    }

    // --- Profile Update Logic ---
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        // Avatar Preview Logic
        const avatarInput = editProfileForm.querySelector('input[name="avatar"]');
        const avatarPreview = editProfileForm.querySelector('.user-avatar-preview');
        
        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => avatarPreview.src = e.target.result;
                    reader.readAsDataURL(file);
                }
            });
        }

        console.log('Profile edit form found and initialized');
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const msg = this.querySelector('.profile-update-message');
            const originalText = btn.innerText;
            
            btn.disabled = true;
            btn.innerText = 'Сохранение...';

            const formData = new FormData(this);
            formData.append('action', 'gh_ajax_update_profile');
            
            const nonceField = this.querySelector('[name="profile_nonce"]');
            if (nonceField) {
                formData.append('nonce', nonceField.value);
            }

            fetch(themeData.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msg.innerText = data.data.message;
                    msg.style.color = '#2ecc71';
                    msg.style.display = 'block';
                    
                    // Update names on page
                    const nameElements = document.querySelectorAll('.profile-header__info .name, .header__user-name');
                    nameElements.forEach(el => el.innerText = data.data.new_name);

                    // Update description on page
                    const bioElement = document.querySelector('.profile-header__info .profile-bio');
                    if (bioElement) {
                        if (data.data.new_description) {
                            bioElement.innerText = data.data.new_description;
                            bioElement.style.display = 'block';
                        } else {
                            bioElement.style.display = 'none';
                        }
                    }

                    // Update avatar on page
                    if (data.data.new_avatar) {
                        const avatarElements = document.querySelectorAll('.user-avatar-img, .header__user-avatar img, .user-avatar-preview');
                        avatarElements.forEach(el => el.src = data.data.new_avatar);
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    msg.innerText = data.data.message || 'Ошибка обновления';
                    msg.style.color = '#ff4d4d';
                    msg.style.display = 'block';
                }
                btn.disabled = false;
                btn.innerText = originalText;
            })
            .catch(err => {
                console.error('Update error:', err);
                msg.innerText = 'Ошибка соединения.';
                msg.style.color = '#ff4d4d';
                msg.style.display = 'block';
                btn.disabled = false;
                btn.innerText = originalText;
            });
        });
    }

    // --- Account Deletion Logic (GitHub style) ---
    const deleteModal = document.getElementById('deleteAccountModal');
    const triggerDelete = document.getElementById('triggerDeleteAccount');
    const closeDelete = document.getElementById('closeDeleteModal');
    const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');

    if (triggerDelete && deleteModal) {
        triggerDelete.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Delete modal triggered');
            const editModal = document.getElementById('editProfileModal');
            if (editModal) editModal.classList.remove('is-open');
            deleteModal.classList.add('is-open');
        });
    }

    if (closeDelete && deleteModal) {
        closeDelete.addEventListener('click', () => {
            deleteModal.classList.remove('is-open');
            if (typeof lenis !== 'undefined') lenis.start();
        });
    }

    // Live name check
    document.addEventListener('input', (e) => {
        if (e.target.id === 'confirmNameInput') {
            const expected = document.getElementById('expectedName')?.value;
            const btn = document.getElementById('deleteSubmitBtn');
            if (btn) {
                if (e.target.value === expected) {
                    btn.disabled = false;
                    btn.style.background = '#FF4B4B';
                    btn.style.color = 'white';
                    btn.style.cursor = 'pointer';
                } else {
                    btn.disabled = true;
                    btn.style.background = '#F0F0F0';
                    btn.style.color = '#999';
                    btn.style.cursor = 'not-allowed';
                }
            }
        }
    });

    // Global submit listener for deletion
    document.addEventListener('submit', (e) => {
        if (e.target.id === 'deleteAccountForm') {
            console.log('Delete form SUBMITTED globally');
            e.preventDefault();
            
            const form = e.target;
            const btn = document.getElementById('deleteSubmitBtn');
            const msg = form.querySelector('.profile-delete-message');
            const expected = document.getElementById('expectedName')?.value;
            const confirmInput = document.getElementById('confirmNameInput');

            if (confirmInput.value !== expected) {
                console.warn('Name mismatch on global submit');
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Удаление...';

            const formData = new FormData();
            formData.append('action', 'gh_ajax_delete_account');
            
            const nonceField = document.querySelector('[name="profile_nonce"]');
            if (nonceField) {
                formData.append('nonce', nonceField.value);
            }

            console.log('Sending AJAX deletion request...');

            fetch(themeData.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    msg.innerText = data.data.message;
                    msg.style.color = '#2ecc71';
                    msg.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1000);
                } else {
                    msg.innerText = data.data.message;
                    msg.style.color = '#ff4d4d';
                    msg.style.display = 'block';
                    btn.disabled = false;
                    btn.innerText = 'Удалить мой аккаунт навсегда';
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                msg.innerText = 'Ошибка соединения.';
                msg.style.color = '#ff4d4d';
                msg.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Удалить мой аккаунт навсегда';
            });
        }
    });

    // --- Forum AJAX Comments Logic ---
    const commentForm = document.getElementById('commentform');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'submit_comment');
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = 'Отправка...';
            
            try {
                const response = await fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();
                
                if (res.success) {
                    this.querySelector('textarea').style.borderColor = '#eee';
                    const parentId = res.data.parent;
                    const commentHtml = res.data.html;
                    
                    if (parentId && parentId !== '0') {
                        // Reply to a comment
                        const parentComment = document.getElementById('comment-' + parentId);
                        let childrenList = parentComment.querySelector('.children');
                        if (!childrenList) {
                            childrenList = document.createElement('div');
                            childrenList.className = 'children';
                            parentComment.appendChild(childrenList);
                        }
                        childrenList.insertAdjacentHTML('beforeend', commentHtml);
                        
                        // Reset form position (Standard WP Comment Reply logic)
                        const cancelBtn = document.getElementById('cancel-comment-reply-link');
                        if (cancelBtn) cancelBtn.click();
                    } else {
                        // Top level comment
                        const commentsList = document.querySelector('.comments-list');
                        if (commentsList) {
                            commentsList.insertAdjacentHTML('beforeend', commentHtml);
                        } else {
                            // If first comment, we might need a reload or to create the container
                            location.reload();
                        }
                    }
                    
                    this.reset();
                    
                } else {
                    // Simple text warning instead of alert
                    let errorMsg = this.querySelector('.comment-error-msg');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'comment-error-msg';
                        errorMsg.style.color = '#ff4d4d';
                        errorMsg.style.fontSize = '14px';
                        errorMsg.style.marginBottom = '15px';
                        errorMsg.style.fontWeight = '500';
                        this.querySelector('.comment-form-field').after(errorMsg);
                    }
                    errorMsg.innerText = res.data.message || 'Ошибка при отправке';
                    this.querySelector('textarea').style.borderColor = '#ff4d4d';
                }
            } catch (err) {
                console.error('Comment error:', err);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        });
    }

    // Delete Comment Logic
    document.body.addEventListener('click', async (e) => {
        if (e.target.classList.contains('delete-comment-btn')) {
            if (!confirm('Вы уверены, что хотите удалить этот ответ?')) return;
            
            const btn = e.target;
            const id = btn.getAttribute('data-id');
            const commentBody = btn.closest('.forum-post-card');
            
            const formData = new FormData();
            formData.append('action', 'delete_forum_comment');
            formData.append('id', id);
            
            try {
                btn.disabled = true;
                const response = await fetch(themeData.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();
                
                if (res.success) {
                    // Update the comment text in the UI
                    const content = commentBody.querySelector('.forum-post-card__excerpt');
                    if (content) content.innerHTML = '<i>Этот ответ был удален автором.</i>';
                    
                    // Remove buttons
                    const footer = commentBody.querySelector('.forum-post-card__footer');
                    if (footer) footer.remove();
                } else {
                    alert(res.data.message);
                }
            } catch (err) {
                console.error('Delete error:', err);
            }
        }
    });

    // Clear error on input
    const forumTextarea = document.querySelector('#commentform textarea');
    if (forumTextarea) {
        forumTextarea.addEventListener('input', function() {
            this.style.borderColor = '#eee';
            const msg = this.closest('form').querySelector('.comment-error-msg');
            if (msg) msg.remove();
        });
    }

    console.log('Gymnastics Hub Scripts Initialized!');
});
