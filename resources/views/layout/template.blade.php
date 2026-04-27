<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>tiMovie - @yield('title', 'Website')</title>
    <link href="/bootstrap/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    @include('partials.navbar')

      <div class="container my-2">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>    
        @endif

        @yield('content')
      </div>

      @include('partials.footer')

    <script src="/bootstrap/bootstrap.bundle.min.js"></script>
  </body>
</html>