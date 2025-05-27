<nav
    class="navbar z-40 navbar-expand-md p-4 navbar-light fixed w-full bottom-0 bg-skl-black border-t-skl-grey border-t shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button id="burger-menu-btn"
            class="navbar-toggler z-50 border border-skl-purple rounded-full p-1 px-2 absolute right-0 bottom-24 mr-2 bg-skl-black"
            type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <iconify-icon class="text-skl-pink transition-all icony" icon="tabler:menu-3" width="32"
                height="32"></iconify-icon>
        </button>

        <div id="mobile-menu"
            class="absolute rounded-sm transition-all translate-x-full right-0 bottom-16 min-h-44 bg-skl-black-90 border border-skl-pink w-10/12 p-4"
            id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    {{-- @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif --}}
                    <li class="p-1">
                        <a class=""
                            href="/">Home</a>
                    </li>
                    <li class="p-1">
                        <a class=""
                            href="/blog">Blog</a>
                    </li>
                    <li class="p-1">
                        <a class=""
                            href="/portfolio">Portfolio</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
