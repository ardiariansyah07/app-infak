<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        @yield('title','Sistem Infak')
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    @include('components.styles')

</head>

<body>

    @include('components.sidebar')

    <div id="content"
         class="content">

        @include('components.topbar')

        <div class="page">

            @yield('content')

        </div>

    </div>

    @include('components.scripts')

</body>
</html>