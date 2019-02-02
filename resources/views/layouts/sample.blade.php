<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="test laravel 5.7">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test workers</title>
    @yield('head')
    @stack('scripts_head')
  </head>
  <body>
    @yield('header')

    @yield('content')

    @yield('footer')

    @stack('scripts')
  </body>
</html>
