<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-infak.png') }}">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link href="{{ asset('css/login.css') }}"
          rel="stylesheet">

</head>

<body>

    {{ $slot ?? '' }}

    @yield('content')

<script>
document.querySelectorAll('[data-password-checklist]').forEach(checklist => {
    const input = document.getElementById(checklist.dataset.passwordChecklist);

    if(!input){
        return;
    }

    const rules = {
        length: value => value.length >= 8,
        upper: value => /[A-Z]/.test(value),
        lower: value => /[a-z]/.test(value),
        symbol: value => /[^A-Za-z0-9]/.test(value),
    };

    const updateChecklist = () => {
        Object.entries(rules).forEach(([rule, passes]) => {
            const item = checklist.querySelector(`[data-rule="${rule}"]`);
            const icon = item?.querySelector('i');

            if(!item || !icon){
                return;
            }

            item.classList.toggle('valid', passes(input.value));
            icon.className = passes(input.value) ? 'bi bi-check-circle-fill' : 'bi bi-circle';
        });
    };

    input.addEventListener('input', updateChecklist);
    updateChecklist();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
