<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Cabang')</title>

    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous"
    />

    {{-- VITE — CABANG ONLY --}}
    @vite(['resources/scss/cabang.scss', 'resources/js/app.js'])

    <script src="{{ asset('assets/js/jamaah-form.js') }}"></script>
    @stack('styles')
</head>

<body data-app="cabang">

<div class="cabang-app">

    {{-- ===============================
       SHELL (GRID)
    =============================== --}}
    <div class="c-shell">

      <aside class="c-sidebar">
        @include('layouts.cabang.sidebar')
      </aside>

      <div class="c-main">

        <header class="c-header">
          @include('layouts.cabang.navbar')
        </header>

        <main class="c-content">
          @yield('content')
        </main>

        <footer class="c-footer">
          @include('layouts.cabang.footer')
        </footer>

      </div>

    </div>

</div>

{{-- BOTTOM MENU (MOBILE ONLY) --}}
@include('layouts.cabang.bottom-menu')

{{-- CABANG — GLOBAL UI JS --}}
<script>
(function () {
  window.openModal = function (id) {
    const modal = document.getElementById(id)
    if (!modal) return
    modal.classList.add('is-open')
    document.body.style.overflow = 'hidden'
  }

  window.closeModal = function (el) {
    const modal = el.closest('.c-modal')
    if (!modal) return
    modal.classList.remove('is-open')
    document.body.style.overflow = ''
  }

  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-modal-close]')) {
      const modal = e.target.closest('.c-modal')
      if (modal) {
        modal.classList.remove('is-open')
        document.body.style.overflow = ''
      }
      return
    }

    if (e.target.classList.contains('c-modal')) {
      e.target.classList.remove('is-open')
      document.body.style.overflow = ''
    }
  })

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.c-modal.is-open')
        .forEach(m => {
          m.classList.remove('is-open')
          document.body.style.overflow = ''
        })
    }
  })
})()
</script>

@stack('scripts')

</body>
</html>
