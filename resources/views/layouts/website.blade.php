<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO BASIC --}}
    <title>@yield('title', 'Hasan Tour & Travel')</title>
    <meta name="description"
          content="@yield('meta_description', 'Program Umrah terpercaya, nyaman, dan sesuai sunnah bersama Hasan Tour & Travel')">

    {{-- WEBSITE CSS (ISOLATED) --}}
    @vite([
        'resources/scss/website/website.scss',
        'resources/js/app.js'
    ])
</head>

<body class="website-body">

    {{-- ===============================
       HEADER
    =============================== --}}
    <header class="site-header">
        <div class="container site-header-inner">
            <div class="site-brand">
                <div class="brand-title">Hasan Tour</div>
                <div class="brand-subtitle">Umrah & Travel</div>
            </div>
        </div>
    </header>

    {{-- ===============================
       MAIN CONTENT
    =============================== --}}
    <main class="site-main">
        @yield('content')
    </main>

    {{-- ===============================
       FOOTER
    =============================== --}}
    <footer class="site-footer">
        <div class="container">
            <p>
                © {{ date('Y') }} Hasan Tour & Travel · Umrah Aman & Terpercaya
            </p>
        </div>
    </footer>

</body>
</html>
