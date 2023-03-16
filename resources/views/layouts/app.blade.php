<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>AgencyAnalytics - @yield('title')</title>
</head>
<body>

<div class="container mx-auto">
    @yield('content')
</div>
</body>
</html>