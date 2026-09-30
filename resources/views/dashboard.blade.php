<x-base>


    <div class="flex h-screen" style="background-image:
        url(https://images.unsplash.com/photo-1593672755342-741a7f868732?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80)">
        <div class="m-auto bg-white rounded p-4" style="width: 640px; height: 480px;">
            <canvas id="exchangeRateChart" data-series="{{ json_encode($series) }}"></canvas>
        </div>
    </div>

</x-base>
