<x-base>


    <div class="flex h-screen" style="background-image:
        url(https://images.unsplash.com/photo-1593672755342-741a7f868732?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80)">
        <div class="m-auto bg-white rounded">
            <canvas id="exchangeRateChart" width="400" height="400"></canvas>
        </div>
    </div>

    <script>
        var ctx         = document.getElementById('exchangeRateChart').getContext('2d');
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($rates as $rate)
                        '{{ $rate->rate_date }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: "Exchange Rate",
                        fillColor: "rgba(52,144,120,0.5)",
                        backgroundColor: "rgba(52,144,120,0.5)",
                        strokeColor: "rgba(220,220,220,0.8)",
                        highlightFill: "rgba(52,144,120,0.6)",
                        data: [
                            @foreach($rates as $rate)
                                '{{ $rate->rate }}',
                            @endforeach
                        ]
                    }
                ],
            },
            options: {}
        });
    </script>


</x-base>
