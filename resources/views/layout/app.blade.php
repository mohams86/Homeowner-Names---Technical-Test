<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
 
    <title>Laravel Vue JS File Upload Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js']) 
</head>
<body>
    <div id="app"> 
        <main class="py-4">
            @yield('content')
        </main> 
    </div>
</body>
</html>