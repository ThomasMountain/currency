<x-base>


    <div class="flex h-screen" style="background-image:
        url(https://images.unsplash.com/photo-1593672755342-741a7f868732?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80)">
        <div class="m-auto bg-white rounded p-4" style="width: 640px; height: 480px;">
            <canvas id="exchangeRateChart"></canvas>
        </div>
    </div>

    <script>
        // Js::from keeps the values numeric so Chart.js draws a real time
        // series, and keeps each currency pair as a separate dataset.
        const series = {{ Illuminate\Support\Js::from($series) }};
        const palette = [
            'rgba(52,144,120,1)',
            'rgba(220,80,80,1)',
            'rgba(60,120,200,1)',
            'rgba(200,150,40,1)',
        ];

        const labels = Object.values(series)[0]?.labels ?? [];

        const ctx = document.getElementById('exchangeRateChart').getContext('2d');
        const myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: Object.entries(series).map(([pair, data], index) => {
                    const colour = palette[index % palette.length];

                    return {
                        label: pair.replace('_', ' / '),
                        fill: false,
                        backgroundColor: colour,
                        borderColor: colour,
                        pointRadius: 2,
                        tension: 0.25,
                        data: data.values,
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
            }
        });
    </script>


</x-base>
