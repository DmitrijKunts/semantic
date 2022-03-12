<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    @yield('canonical')

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    @include('gtag')
</head>

<body>
    @include('header')

    @yield('content')

    @include('footer')
</body>

</html>
