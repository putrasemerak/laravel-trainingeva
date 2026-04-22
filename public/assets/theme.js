/* ============================================
   AIN System — Theme Management
   ============================================ */

(function() {
    const ThemeManager = {
        init: function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            this.apply(savedTheme);
        },

        apply: function(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Dispatch event for components that need to react
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: theme } }));
        },

        toggle: function() {
            const current = document.documentElement.getAttribute('data-theme');
            const target = current === 'dark' ? 'light' : 'dark';
            this.apply(target);
            return target;
        }
    };

    // Global expose
    window.AINTheme = ThemeManager;
    
    // Immediate execution to prevent flash
    ThemeManager.init();
})();
