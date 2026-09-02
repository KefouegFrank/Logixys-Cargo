import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// focus powers x-trap on the mobile drawer, which keeps tabbing inside the panel.
Alpine.plugin(focus);

/*
 * Hero slideshow. Autoplay advances on a coarse tick rather than one long
 * timer, so the progress bar and pause-on-hover share a single source of
 * truth. Reduced-motion visitors get a static first slide they drive manually.
 */
Alpine.data('heroCarousel', (count, interval) => ({
    count,
    interval,
    active: 0,
    elapsed: 0,
    paused: false,
    stopped: false,
    timer: null,
    touchX: null,

    get reduced() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    },

    get progress() {
        return this.stopped ? 0 : (this.elapsed / this.interval) * 100;
    },

    init() {
        if (this.reduced) {
            this.stopped = true;

            return;
        }

        this.timer = setInterval(() => this.tick(), 50);
        this.$watch('active', () => (this.elapsed = 0));
    },

    destroy() {
        clearInterval(this.timer);
    },

    tick() {
        if (this.paused || this.stopped || document.hidden) {
            return;
        }

        this.elapsed += 50;

        if (this.elapsed >= this.interval) {
            this.go(this.active + 1);
        }
    },

    go(index) {
        this.active = (index + this.count) % this.count;
    },

    // Any deliberate control use hands the slideshow over to the visitor.
    select(index) {
        this.go(index);
        this.stopped = true;
    },

    next() {
        this.select(this.active + 1);
    },

    prev() {
        this.select(this.active - 1);
    },

    onTouchStart(event) {
        this.touchX = event.changedTouches[0].clientX;
    },

    onTouchEnd(event) {
        if (this.touchX === null) {
            return;
        }

        const delta = event.changedTouches[0].clientX - this.touchX;
        this.touchX = null;

        if (Math.abs(delta) > 50) {
            delta < 0 ? this.next() : this.prev();
        }
    },
}));

/*
 * Language switching is a normal navigation, so the browser would land the new
 * page at the top. Stash the offset on the way out and put it back on the way
 * in; the head script has kept the page blank until this runs.
 */
const LOCALE_SCROLL_KEY = 'logixys:locale-scroll';

document.addEventListener('click', (event) => {
    if (! event.target.closest('a[data-locale-link]')) {
        return;
    }

    try {
        sessionStorage.setItem(LOCALE_SCROLL_KEY, String(window.scrollY));
    } catch (e) {
        // Private mode with storage disabled: fall back to a normal navigation.
    }
});

function restoreLocaleScroll() {
    let offset = null;

    try {
        offset = sessionStorage.getItem(LOCALE_SCROLL_KEY);
        sessionStorage.removeItem(LOCALE_SCROLL_KEY);
    } catch (e) {
        // Ignore: nothing to restore.
    }

    if (offset !== null) {
        // 'instant' so the smooth scroll-behavior on <html> doesn't animate it.
        window.scrollTo({ top: parseInt(offset, 10) || 0, behavior: 'instant' });
    }

    document.documentElement.classList.remove('is-switching-locale');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', restoreLocaleScroll);
} else {
    restoreLocaleScroll();
}

window.Alpine = Alpine;
Alpine.start();
