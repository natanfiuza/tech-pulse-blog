<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">


    <script>
        (function() {
            try {
                var tema_salvo = localStorage.getItem('techpulse_tema');
                var prefere_escuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (tema_salvo === 'dark' || (!tema_salvo && prefere_escuro)) {
                    document.documentElement.classList.add('dark');
                } else if (tema_salvo === 'light') {
                    document.documentElement.classList.remove('dark');
                } else {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
