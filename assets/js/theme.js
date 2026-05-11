/**
 * GreenTrans - Theme Switcher
 * Dark/Light mode toggle with localStorage persistence
 */

(function() {
    const STORAGE_KEY = 'gt-theme';
    
    // Get saved theme or default to light
    function getSavedTheme() {
        return localStorage.getItem(STORAGE_KEY) || 'light';
    }
    
    // Apply theme to document
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        
        // Update toggle button icons
        const toggleBtns = document.querySelectorAll('.gt-theme-toggle');
        toggleBtns.forEach(btn => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
        });
    }
    
    // Toggle theme
    function toggleTheme() {
        const current = getSavedTheme();
        const next = current === 'light' ? 'dark' : 'light';
        applyTheme(next);
    }
    
    // Initialize on page load
    applyTheme(getSavedTheme());
    
    // Bind click events after DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.gt-theme-toggle').forEach(btn => {
            btn.addEventListener('click', toggleTheme);
        });
    });
    
    // Expose globally
    window.GTTheme = { toggle: toggleTheme, apply: applyTheme, get: getSavedTheme };
})();
