/**
 * GreenTrans - Main JavaScript
 * Global utilities, toast notifications, sidebar toggle
 */

document.addEventListener('DOMContentLoaded', function() {

    // === SIDEBAR TOGGLE ===
    const sidebarToggle = document.querySelector('.gt-sidebar-toggle');
    const body = document.body;
    const sidebar = document.querySelector('.gt-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('show');
            } else {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('gt-sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    // Restore sidebar state
    if (localStorage.getItem('gt-sidebar-collapsed') === 'true' && window.innerWidth > 991) {
        body.classList.add('sidebar-collapsed');
    }

    // === NOTIFICATION DROPDOWN ===
    const notifBtn = document.querySelector('.gt-notif-btn');
    const notifDropdown = document.querySelector('.gt-notif-dropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('show');
            }
        });
    }

    // === ANIMATED COUNTERS ===
    const counters = document.querySelectorAll('.kpi-value[data-count]');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => counterObserver.observe(counter));

    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-count'));
        const prefix = el.getAttribute('data-prefix') || '';
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = 1500;
        const start = 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const current = Math.floor(start + (target - start) * eased);
            
            el.textContent = prefix + formatIndian(current) + suffix;
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        requestAnimationFrame(update);
    }

    // === INDIAN NUMBER FORMAT ===
    function formatIndian(num) {
        const str = num.toString();
        const len = str.length;
        if (len <= 3) return str;
        
        const last3 = str.slice(-3);
        const remaining = str.slice(0, -3);
        const formatted = remaining.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
        return formatted + ',' + last3;
    }

    // === TOAST NOTIFICATIONS ===
    window.GTToast = {
        container: null,

        init() {
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.className = 'gt-toast-container';
                document.body.appendChild(this.container);
            }
        },

        show(message, type = 'info', duration = 4000) {
            this.init();
            
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };

            const toast = document.createElement('div');
            toast.className = `gt-toast ${type}`;
            toast.innerHTML = `
                <i class="bi ${icons[type] || icons.info}" style="font-size:1.2rem"></i>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:0.85rem">${message}</div>
                </div>
                <button class="gt-toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;

            this.container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        },

        success(msg) { this.show(msg, 'success'); },
        error(msg) { this.show(msg, 'error'); },
        warning(msg) { this.show(msg, 'warning'); },
        info(msg) { this.show(msg, 'info'); }
    };

    // === SCROLL ANIMATIONS ===
    const animElements = document.querySelectorAll('.animate-on-scroll');
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slide-up');
                scrollObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    animElements.forEach(el => scrollObserver.observe(el));

    // Show flash messages as toasts
    const flashEl = document.querySelector('[data-flash]');
    if (flashEl) {
        const type = flashEl.getAttribute('data-flash-type');
        const msg = flashEl.getAttribute('data-flash');
        if (msg) GTToast.show(msg, type || 'info');
    }
});
