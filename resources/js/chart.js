import Chart from 'chart.js/auto';

const PALETTE = [
    'rgba(52,144,120,1)',
    'rgba(220,80,80,1)',
    'rgba(60,120,200,1)',
    'rgba(200,150,40,1)',
];

/**
 * Draws one line per currency pair from the series embedded on the canvas
 * by the dashboard view. Safe to call more than once.
 */
export function renderChart() {
    const canvas = document.getElementById('exchangeRateChart');

    if (!canvas || !canvas.dataset.series) {
        return null;
    }

    const series = JSON.parse(canvas.dataset.series);
    const pairs = Object.entries(series);

    if (pairs.length === 0) {
        return null;
    }

    // Tear down any previous instance, otherwise Chart.js throws when the
    // canvas is already in use.
    if (canvas.chart) {
        canvas.chart.destroy();
    }

    canvas.chart = new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: pairs[0][1].labels,
            datasets: pairs.map(([pair, points], index) => {
                const colour = PALETTE[index % PALETTE.length];

                return {
                    label: pair.replace('_', ' / '),
                    fill: false,
                    backgroundColor: colour,
                    borderColor: colour,
                    pointRadius: 2,
                    tension: 0.25,
                    data: points.values,
                };
            }),
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { ticks: { maxTicksLimit: 10 } },
            },
        },
    });

    return canvas.chart;
}

// Vite emits module scripts, which execute before DOMContentLoaded. Check
// readyState anyway so this still works if the bundle is loaded late.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderChart);
} else {
    renderChart();
}
