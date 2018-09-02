<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Currency Converter</title>
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="{{ mix('css/main.css') }}">
</head>
<body>

<div class="container-fluid" id="app">
@include('partials.nav')

@yield('content')

</div>

</body>


<script src="{{ mix('js/app.js') }}"></script>
@stack('scripts')


</html>