// JavaScript to toggle the navigation menu
const menuButton = document.getElementById('menu-button');
const navLinks = document.getElementById('nav-links');

menuButton.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

// Initialize Charts
document.addEventListener('DOMContentLoaded', function () {
    // Mining Chart
    const miningCtx = document.getElementById('miningChart').getContext('2d');
    const miningChart = new Chart(miningCtx, {
        type: 'line',
        data: {
            labels: Array.from({
                length: 30
            }, (_, i) => `${i + 1} Oct`),
            datasets: [{
                label: 'BTC Earnings',
                data: [0.001, 0.0012, 0.0015, 0.0013, 0.0014, 0.0016, 0.0018, 0.002, 0.0022, 0.0025, 0.0023, 0.0024, 0.0026, 0.0028, 0.003, 0.0032, 0.0035, 0.0038, 0.004, 0.0042, 0.0045, 0.0048, 0.005, 0.0052, 0.0055, 0.0058, 0.006, 0.0062, 0.0065, 0.0068],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        }
    });

    // Make chart responsive on window resize
    window.addEventListener('resize', function () {
        miningChart.resize();
    });
});