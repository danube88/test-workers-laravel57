<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
