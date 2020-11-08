<!DOCTYPE html>
<html lang="en">
  <head>
    @include('v2.layout.partials.head_admin')
  </head>

  <body>
  @if(!Route::is(['login','register','forgot-password','lock-screen','error-404','error-500']))
  @include('v2.layout.partials.header_admin')
 @include('v2.layout.partials.nav_admin')
 @endif
 @yield('content')
 @include('v2.layout.partials.footer_admin-scripts')


  </body>
</html>
