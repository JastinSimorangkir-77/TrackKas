import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('sidebar', () => ({
    collapsed: document.documentElement.dataset.sidebar === 'collapsed',

    toggle() {
        this.collapsed = !this.collapsed;

        document.documentElement.dataset.sidebar = this.collapsed ? 'collapsed' : 'expanded';

        try {
            localStorage.setItem('trackkas-sidebar', document.documentElement.dataset.sidebar);
        } catch (error) {
            // Storage can be unavailable (e.g. private mode): the sidebar then
            // simply opens expanded again on the next page load.
        }
    },
}));

Alpine.start();
