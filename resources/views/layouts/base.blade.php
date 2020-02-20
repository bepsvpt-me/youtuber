<!DOCTYPE html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'YouTuber')</title>
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css?v=4.4.1') }}">
    @yield('style')
    <link rel="stylesheet" href="{{ mix('/css/app.css')  }}">
  </head>
  <body class="container-fluid d-flex flex-column">
    <header class="pt-3">
      @yield('header')
    </header>

    <hr class="w-100">

    <main class="flex-grow-1">
      @yield('main')
    </main>

    <footer class="text-center">
      <a href="https://github.com/bepsvpt-me/youtuber">GitHub</a>
      <p>bepsvpt.me © {{ date('Y') }}</p>
    </footer>

    @yield('script')
  </body>
</html>