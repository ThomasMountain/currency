@extends('app')
@section('content')


<div class="container">

</div>

@push('scripts')
<script>
var myLineChart = new Chart(ctx, {
    type: 'line',
    data:{
     labels:[
     @foreach($rates as $rate)
        "{{$rate->rate_date}}",
     @endforeach
     ],
    data:[
        @foreach($rates as $rate)
       "{{$rate->rate}}",
     @endforeach
    ]
    },
    options: options
});
</script>
@endpush

@endsection