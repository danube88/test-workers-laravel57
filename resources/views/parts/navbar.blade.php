<nav class="navbar navbar-expand-md navbar-light fixed-top bg-light">
  <a class="navbar-brand" href="#">Test</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbars">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item hierarchy">
        <a class="nav-link" href="{{ route('hierarchy') }}">Иерархия</a>
      </li>
      <li class="nav-item list">
        <a class="nav-link" href="{{ route('list') }}">Список</a>
      </li>
    </ul>
    <ul class="navbar-nav px-3">
      @guest
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
        </li>
        @if (Route::has('register'))
          <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
          </li>
        @endif
      @else
        <span class="navbar-brand">{{ Auth::user()->name }}</span>
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                     document.getElementById('logout-form').submit();">
            Выход
          </a>
          {!! Form::open(['route' => 'logout', 'id' => 'logout-form']) !!}
          {!! Form::close() !!}
        </li>
      @endguest
    </ul>
  </div>
</nav>
