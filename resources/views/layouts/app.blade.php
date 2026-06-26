<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        @yield('title','Sistem Infak')
    </title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-infak.png') }}">

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

            @php($breadcrumbItems = \App\Support\Breadcrumbs::items())

            @if($breadcrumbItems)
                <div class="app-breadcrumb">
                    @include('components.breadcrumb', ['items' => $breadcrumbItems])
                </div>
            @endif

            @yield('content')

        </div>

        <footer class="app-footer">
            © {{ date('Y') }} Sistem Informasi Infak Sekolah · {{ \App\Support\AppVersion::current() }} · Dikembangkan oleh
            <a
                href="https://www.instagram.com/ardiariansyah07"
                target="_blank"
                rel="noopener noreferrer">
                Ardi Ariansyah
            </a>
        </footer>

    </div>

    @include('components.scripts')

</body>
</html>
