/*
 * Storefront behaviour: mobile drawer, categories dropdown, hero
 * carousel, listing filters and the quantity selector.
 * No inline handlers anywhere, so a strict Content Security Policy applies.
 */
(function () {
    'use strict';

    document.documentElement.classList.add('js');

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';

    /* ---------- Mobile drawer ---------- */
    function initDrawer() {
        const drawer = document.querySelector('[data-drawer]');
        const opener = document.querySelector('[data-drawer-open]');
        if (!drawer || !opener) return;

        const panel = drawer.querySelector('.drawer-panel');

        function focusables() {
            return Array.from(panel.querySelectorAll(FOCUSABLE));
        }

        function open() {
            drawer.hidden = false;
            opener.setAttribute('aria-expanded', 'true');
            document.body.classList.add('has-drawer-open');
            const first = focusables()[0];
            if (first) first.focus();
            document.addEventListener('keydown', onKeydown);
        }

        function close() {
            drawer.hidden = true;
            opener.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('has-drawer-open');
            document.removeEventListener('keydown', onKeydown);
            opener.focus();
        }

        function onKeydown(event) {
            if (event.key === 'Escape') {
                close();
                return;
            }
            if (event.key !== 'Tab') return;

            // Keep focus inside the drawer while it is open.
            const items = focusables();
            const first = items[0];
            const last = items[items.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }

        opener.addEventListener('click', open);
        drawer.querySelectorAll('[data-drawer-close]').forEach(function (el) {
            el.addEventListener('click', close);
        });

        // Close if the viewport grows past the mobile layout.
        window.matchMedia('(min-width: 992px)').addEventListener('change', function (mq) {
            if (mq.matches && !drawer.hidden) close();
        });
    }

    /* ---------- Desktop categories dropdown ---------- */
    function initDropdowns() {
        document.querySelectorAll('[data-dropdown]').forEach(function (dropdown) {
            const toggle = dropdown.querySelector('[data-dropdown-toggle]');
            const menu = dropdown.querySelector('.dropdown-menu');
            if (!toggle || !menu) return;

            function setOpen(isOpen) {
                toggle.setAttribute('aria-expanded', String(isOpen));
                menu.hidden = !isOpen;
            }

            toggle.addEventListener('click', function () {
                setOpen(menu.hidden);
            });
            document.addEventListener('click', function (event) {
                if (!dropdown.contains(event.target)) setOpen(false);
            });
            dropdown.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !menu.hidden) {
                    setOpen(false);
                    toggle.focus();
                }
            });
            dropdown.addEventListener('focusout', function (event) {
                if (!dropdown.contains(event.relatedTarget)) setOpen(false);
            });
        });
    }

    /* ---------- Hero carousel ---------- */
    function initCarousel() {
        const carousel = document.querySelector('[data-carousel]');
        if (!carousel) return;

        const slides = Array.from(carousel.querySelectorAll('[data-slide]'));
        const dots = Array.from(carousel.querySelectorAll('[data-slide-to]'));
        if (slides.length < 2) return;

        let current = 0;
        let timer = null;

        function show(index) {
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, i) {
                slide.hidden = i !== current;
                slide.classList.toggle('is-active', i === current);
            });
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === current);
                if (i === current) dot.setAttribute('aria-current', 'true');
                else dot.removeAttribute('aria-current');
            });
        }

        function start() {
            if (reducedMotion.matches || timer) return;
            timer = window.setInterval(function () { show(current + 1); }, 6000);
        }

        function stop() {
            window.clearInterval(timer);
            timer = null;
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                show(Number(dot.dataset.slideTo));
                stop();
                start();
            });
        });

        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);
        carousel.addEventListener('focusin', stop);
        carousel.addEventListener('focusout', start);
        reducedMotion.addEventListener('change', function () {
            if (reducedMotion.matches) stop();
            else start();
        });

        start();
    }

    /* ---------- Listing filters ---------- */
    function initFilters() {
        const form = document.querySelector('[data-filters]');
        if (!form) return;

        const toggle = form.querySelector('[data-filter-toggle]');
        const panel = form.querySelector('.filter-panel');

        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                const isOpen = panel.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', String(isOpen));
            });
        }

        // On desktop, changing a select applies it straight away.
        form.querySelectorAll('[data-auto-submit]').forEach(function (select) {
            select.addEventListener('change', function () {
                if (!panel || !panel.classList.contains('is-open')) form.submit();
            });
        });
    }

    /* ---------- Quantity selector and demo cart ---------- */
    function initQuantity() {
        document.querySelectorAll('[data-qty]').forEach(function (wrapper) {
            const input = wrapper.querySelector('input');
            if (!input) return;

            function clamp(value) {
                const min = Number(input.min) || 1;
                const max = Number(input.max) || min;
                const n = Math.round(Number(value));
                return Math.min(max, Math.max(min, isNaN(n) ? min : n));
            }

            wrapper.querySelectorAll('[data-qty-step]').forEach(function (button) {
                button.addEventListener('click', function () {
                    input.value = clamp(Number(input.value) + Number(button.dataset.qtyStep));
                });
            });
            input.addEventListener('change', function () {
                input.value = clamp(input.value);
            });
        });
    }

    function readCart() {
        try {
            return Math.max(0, parseInt(window.localStorage.getItem('store-cart-count'), 10) || 0);
        } catch (e) {
            return 0;
        }
    }

    function renderCart(count) {
        document.querySelectorAll('[data-cart-count]').forEach(function (el) {
            el.textContent = String(count);
        });
        document.querySelectorAll('[data-cart-link]').forEach(function (el) {
            el.setAttribute('aria-label', 'Cart, ' + count + (count === 1 ? ' item' : ' items'));
        });
    }

    function initCart() {
        renderCart(readCart());

        const form = document.querySelector('[data-add-to-cart]');
        if (!form) return;

        form.addEventListener('submit', function (event) {
            // There is no checkout yet: keep a count in this browser only.
            event.preventDefault();
            const qty = Number(form.querySelector('input[name="quantity"]').value) || 1;
            const count = readCart() + qty;
            try {
                window.localStorage.setItem('store-cart-count', String(count));
            } catch (e) { /* storage unavailable: still update the badge */ }
            renderCart(count);

            const feedback = form.querySelector('[data-cart-feedback]');
            if (feedback) feedback.textContent = 'Added ' + qty + ' to your cart.';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDrawer();
        initDropdowns();
        initCarousel();
        initFilters();
        initQuantity();
        initCart();
    });
})();
