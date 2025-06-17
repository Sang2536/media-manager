<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Media Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

{{--    <link href="../../css/app.css" rel="stylesheet">--}}
    @vite('resources/css/app.css')
</head>
<body>
    @yield('content')
</body>
</html>
