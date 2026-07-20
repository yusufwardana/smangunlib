/*
 * Theme Manager - Frontend behaviour
 * ------------------------------------------------------------------
 * - Sinkronisasi color picker <-> input teks hex
 * - Live preview realtime (AJAX ke route preview, tanpa reload)
 * - Preview upload gambar (logo, favicon, background) sebelum disimpan
 * - Simpan per-grup via AJAX (tanpa reload) + terapkan CSS variables baru
 * - Reset tema dengan konfirmasi
 *
 * Dependensi: jQuery, Bootstrap, SweetAlert2 (dimuat di layouts/app.blade.php)
 */
(function () {
    'use strict';

    const routes = window.ThemeManagerRoutes || {};
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const previewStyleId = 'theme-preview-live';

    /** Terapkan blok CSS variables ke <style id="theme-variables"> (atau style preview khusus). */
    function applyCss(css, live) {
        const targetId = live ? previewStyleId : 'theme-variables';
        let el = document.getElementById(targetId);

        if (!el) {
            el = document.createElement('style');
            el.id = targetId;
            document.head.appendChild(el);
        }

        // Untuk preview live, kita hanya menimpa variabel :root.
        if (live) {
            el.innerHTML = css;
        } else {
            el.innerHTML = css;
        }
    }

    /** Debounce sederhana. */
    function debounce(fn, wait) {
        let t;
        return function () {
            const ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(ctx, args), wait);
        };
    }

    /** Kumpulkan nilai satu form grup menjadi objek {key: value}. */
    function collectGroup(form) {
        const settings = {};
        form.querySelectorAll('[data-theme-key]').forEach((input) => {
            const fullKey = input.getAttribute('data-theme-key'); // group.key
            const key = fullKey.split('.').slice(1).join('.');

            if (input.type === 'checkbox') {
                settings[key] = input.checked ? '1' : '0';
            } else {
                settings[key] = input.value;
            }
        });
        return settings;
    }

    /** Kirim preview realtime. */
    const requestPreview = debounce(function (group, form) {
        if (!routes.preview) return;

        const settings = collectGroup(form);

        fetch(routes.preview, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ group: group, settings: settings }),
        })
            .then((r) => r.json())
            .then((data) => {
                if (data && data.css_variables) {
                    applyCss(data.css_variables, true);
                }
            })
            .catch(() => { /* diam saja pada preview */ });
    }, 150);

    /* ---------- Color picker <-> text ---------- */
    document.querySelectorAll('.theme-color-picker').forEach((picker) => {
        const textSel = picker.getAttribute('data-target');
        const text = textSel ? document.querySelector(textSel) : null;

        picker.addEventListener('input', () => {
            if (text) text.value = picker.value;
            triggerPreview(picker);
        });

        if (text) {
            text.addEventListener('input', () => {
                if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(text.value)) {
                    picker.value = text.value;
                }
                triggerPreview(text);
            });
        }
    });

    /* ---------- Text/select/toggle -> preview ---------- */
    document.querySelectorAll('.theme-text, .theme-select, .theme-toggle').forEach((el) => {
        const evt = (el.type === 'checkbox' || el.tagName === 'SELECT') ? 'change' : 'input';
        el.addEventListener(evt, () => triggerPreview(el));
    });

    function triggerPreview(el) {
        const form = el.closest('.theme-form');
        if (!form) return;
        const group = form.getAttribute('data-group');
        requestPreview(group, form);
    }

    /* ---------- Image upload preview ---------- */
    document.querySelectorAll('.theme-image-input').forEach((input) => {
        input.addEventListener('change', () => {
            const previewSel = input.getAttribute('data-preview');
            const img = previewSel ? document.querySelector(previewSel) : null;
            const file = input.files && input.files[0];

            if (img && file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    });

    /* ---------- AJAX submit per grup ---------- */
    document.querySelectorAll('.theme-form').forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('[type="submit"]');
            const original = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
            }

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data && data.success) {
                        if (data.css_variables) applyCss(data.css_variables, false);
                        notify('success', data.message || 'Tema berhasil disimpan.');
                    } else {
                        notify('error', (data && data.message) || 'Gagal menyimpan tema.');
                    }
                })
                .catch(() => notify('error', 'Terjadi kesalahan jaringan.'))
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = original;
                    }
                });
        });
    });

    /* ---------- Reset theme ---------- */
    const resetBtn = document.getElementById('btn-reset-theme');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            const doReset = () => document.getElementById('reset-theme-form').submit();

            if (window.Swal) {
                Swal.fire({
                    title: 'Reset Tema?',
                    text: 'Seluruh pengaturan tema akan dikembalikan ke default.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e63946',
                    confirmButtonText: 'Ya, reset',
                    cancelButtonText: 'Batal',
                }).then((res) => { if (res.isConfirmed) doReset(); });
            } else if (confirm('Reset seluruh tema ke default?')) {
                doReset();
            }
        });
    }

    function notify(type, message) {
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        } else {
            alert(message);
        }
    }
})();
