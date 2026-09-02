import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// focus powers x-trap on the mobile drawer, which keeps tabbing inside the panel.
Alpine.plugin(focus);

window.Alpine = Alpine;
Alpine.start();
