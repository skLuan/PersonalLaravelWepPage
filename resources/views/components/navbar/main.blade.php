<nav id="mobile-navbar"
    class="navbar z-50 lg:hidden navbar-expand-md p-4 navbar-light fixed w-full bottom-0 bg-skl-white dark:bg-skl-black border-t-skl-grey border-t shadow-sm">
    <div class="container">
        {{-- <a class="navbar-brand" href="{{ url('/') }}">
                Home
            </a> --}}
        <div class="z-50 absolute right-0 bottom-32 flex flex-row transition-all">
            <button id="burger-menu-btn"
                class="navbar-toggler border border-skl-purple rounded-full p-2 mr-4 bg-skl-white-true dark:bg-skl-black flex" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <iconify-icon class="icony text-2xl text-skl-pink transition-all icony m-auto" icon="tabler:menu-3"
                    width="32" height="32"></iconify-icon>
            </button>
            <button id="top-btn"
                class="navbar-toggler flex border border-skl-purple rounded-full p-2 mr-2 bg-skl-white-true dark:bg-skl-black " type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <iconify-icon class="text-2xl text-skl-pink transition-all icony m-auto min-w-8"
                    icon="tabler:arrow-bar-to-up" width="32" height="32"></iconify-icon>
            </button>
        </div>

        <div id="mobile-menu"
            class="absolute rounded-sm transition-all translate-x-full right-0 bottom-16 min-h-44 bg-skl-black-90 border border-skl-pink w-10/12 p-4"
            id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <li class="p-1">
                    <a class="block text-lg no-underline font-bold {{ request()->routeIs('home') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                        href="/" aria-current="page">Home</a>
                </li>
                <li class="p-1">
                    <a class="block text-lg no-underline font-bold {{ request()->routeIs('binshopsblog.index') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                        href="/blog">Blog</a>
                </li>
                <li class="p-1">
                    <a class="block text-lg no-underline font-bold {{ request()->routeIs('portfolio') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                        href="/portfolio">Portfolio</a>
                </li>
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
                @else
                    {{-- <li class="nav-item dropdown">
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
                    </li> --}}
                @endguest
                <li class="pb-2">
                    <h5>Social</h5>
                </li>
                <li class="flex flex-row flex-wrap">
                    <a class="pb-0" href="https://twitter.com/yourprofile" target="_blank"><iconify-icon class="text-2xl"
                            icon="simple-icons:medium" width="24" height="24"></iconify-icon></a>
                    <a class="pb-0" href="https://github.com/yourprofile" target="_blank"><iconify-icon class="text-2xl"
                            icon="akar-icons:github-fill" width="24" height="24"></iconify-icon></a>
                    <a class="pb-0" href="https://linkedin.com/in/yourprofile" target="_blank"><iconify-icon class="text-2xl"
                            icon="icomoon-free:linkedin" width="24" height="24"></iconify-icon></a>
                    <a class="pb-0" href="https://instagram.com/yourprofile" target="_blank"><iconify-icon class="text-2xl"
                            icon="akar-icons:instagram-fill" width="24" height="24"></iconify-icon></a>
                    <a class="pb-0" href="https://facebook.com/yourprofile" target="_blank"><iconify-icon class="text-2xl"
                            icon="akar-icons:facebook-fill" width="24" height="24"></iconify-icon></a>
                    <a class="pb-0" href="mailto:youremail@example.com" target="_blank"><iconify-icon class="text-2xl"
                            icon="akar-icons:mail-fill" width="24" height="24"></iconify-icon></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<nav
    class="hidden lg:flex fixed top-0 z-40 w-full px-52 justify-between items-center p-4 bg-skl-black border-b-skl-grey border-b shadow-sm">
    <div class="flex items-center">
        <a class="navbar-brand text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('home') ? 'active' : '' }}"
            href="{{ url('/') }}">Home</a>
        <span class="text-skl-white ml-2 text-lg font-skl-titles">|</span>
        <span class="text-skl-white ml-2 text-lg font-skl-titles">David Luan Erazo</span>
    </div>
    <ul class="hidden lg:flex justify-center items-center gap-4 p-2">
        <li class="p-1">
            <a class="block font-skl-titles
             text-lg no-underline font-bold {{ request()->routeIs('home') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                href="{{ url('/') }}" aria-current="page">Home</a>
        </li>
        <li class="p-1">
            <a class="block font-skl-titles
             text-lg no-underline font-bold {{ request()->routeIs('binshopsblog.index') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                href="{{ url('/blog') }}">Blog</a>
        </li>
        <li class="p-1">
            <a class="block font-skl-titles
             text-lg no-underline font-bold {{ request()->routeIs('portfolio') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                href="{{ url('/portfolio') }}">Portfolio</a>
        </li>
    </ul>
</nav>
