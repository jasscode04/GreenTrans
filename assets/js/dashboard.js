/**
 * GreenTrans - Dashboard JavaScript
 * Chart configs, dashboard interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // === CHART.JS DEFAULT CONFIG ===
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = getComputedStyle(document.documentElement)
            .getPropertyValue('--gt-text-muted').trim() || '#94a3b8';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.padding = 20;
    }

    // === INIT REVENUE CHART ===
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx && typeof Chart !== 'undefined') {
        const gradient = revenueCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        window.revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: window.revenueData || [45000, 62000, 55000, 78000, 92000, 85000, 110000, 95000, 125000, 140000, 130000, 155000],
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return '₹' + formatIndianChart(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(val) { return '₹' + formatIndianChart(val); }
                        }
                    }
                }
            }
        });
    }

    // === DELIVERY STATS CHART ===
    const deliveryCtx = document.getElementById('deliveryChart');
    if (deliveryCtx && typeof Chart !== 'undefined') {
        window.deliveryChart = new Chart(deliveryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'In Transit', 'Pending', 'Picked Up', 'Cancelled'],
                datasets: [{
                    data: window.deliveryData || [45, 20, 15, 12, 8],
                    backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#3b82f6', '#ef4444'],
                    borderWidth: 0,
                    spacing: 4,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, font: { size: 12 } }
                    }
                }
            }
        });
    }

    // === DRIVER PERFORMANCE CHART ===
    const driverCtx = document.getElementById('driverPerformanceChart');
    if (driverCtx && typeof Chart !== 'undefined') {
        window.driverChart = new Chart(driverCtx, {
            type: 'bar',
            data: {
                labels: window.driverLabels || ['Amit', 'Suresh', 'Vikram', 'Raju', 'Deepak'],
                datasets: [{
                    label: 'Completed',
                    data: window.driverCompleted || [28, 22, 18, 15, 12],
                    backgroundColor: '#10b981',
                    borderRadius: 6
                }, {
                    label: 'Pending',
                    data: window.driverPending || [3, 5, 4, 2, 6],
                    backgroundColor: '#f59e0b',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, beginAtZero: true }
                }
            }
        });
    }

    // === SHIPMENT TREND CHART ===
    const trendCtx = document.getElementById('shipmentTrendChart');
    if (trendCtx && typeof Chart !== 'undefined') {
        window.trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Shipments',
                    data: window.trendData || [35, 48, 42, 56],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, beginAtZero: true }
                }
            }
        });
    }

    // Indian format helper for charts
    function formatIndianChart(num) {
        num = Math.round(num);
        const str = num.toString();
        if (str.length <= 3) return str;
        const last3 = str.slice(-3);
        const remaining = str.slice(0, -3);
        return remaining.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + last3;
    }
});
