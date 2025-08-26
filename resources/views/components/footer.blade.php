<nav id="footer"
    class="bg-skl-grey pb-10 lg:pb-0 text-skl-white flex flex-col items-center border-t-2 border-skl-grey z-50 relative">
    <div class="text-center mb-4 w-full">
        <ul class="flex flex-row items-center justify-center flex-wrap w-full border-b-2 border-skl-black">
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold {{ request()->routeIs('home') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                    href="/" aria-current="page">Home</a>
            </li>
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold {{ request()->routeIs('binshopsblog.index') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                    href="/blog">Blog</a>
            </li>
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold {{ request()->routeIs('portfolio') ? 'active' : '' }} text-skl-pink hover:text-skl-purple transition-all"
                    href="/portfolio">Portfolio</a>
            </li>
        </ul>
        <div class="p-6 lg:flex lg:justify-evenly">
            <p class='mb-1'>This site is a personal project <br> showcasing my work and skills.</p>
            <div>
                <h5>Follow me on</h5>
                <div class="flex flex-row flex-wrap items-center justify-center">
                    <a class="h-fit" href="https://twitter.com/yourprofile" target="_blank"><iconify-icon
                            icon="simple-icons:medium" width="24" height="24"></iconify-icon></a>
                    <a class="h-fit" href="https://github.com/yourprofile" target="_blank"><iconify-icon
                            icon="akar-icons:github-fill" width="24" height="24"></iconify-icon></a>
                    <a class="h-fit" href="https://linkedin.com/in/yourprofile" target="_blank"><iconify-icon
                            icon="icomoon-free:linkedin" width="24" height="24"></iconify-icon></a>
                    <a class="h-fit" href="https://instagram.com/yourprofile" target="_blank"><iconify-icon
                            icon="akar-icons:instagram-fill" width="24" height="24"></iconify-icon></a>
                    <a class="h-fit" href="https://facebook.com/yourprofile" target="_blank"><iconify-icon
                            icon="akar-icons:facebook-fill" width="24" height="24"></iconify-icon></a>
                    <a class="h-fit" href="mailto:youremail@example.com" target="_blank"><iconify-icon
                            icon="akar-icons:mail-fill" width="24" height="24"></iconify-icon></a>
                </div>
            </div>
        </div>
        <div class="border-t border-skl-grey bg-skl-black">

            <p class='text-sm border-b border-skl-grey'>Built with <a href="https://laravel.com" target="_blank"
                    class="text-skl-pink">Laravel</a> and
                <a href="https://binshops.com/laravel-blog-package" target="_blank"
                    class="text-skl-pink">BinshopsBlog</a>.
            </p>
            <p class='text-sm'>© {{ date('Y') }} Luan Erazo. All rights reserved.</p>
        </div>
        {{-- @if (\Auth::check() && \Auth::user()->canManageBinshopsBlogPosts())
            <div class="text-center">
                <p class='mb-1'>You are logged in as a blog admin user.
                    <br>
                    <a href='{{ route('binshopsblog.admin.index') }}' class='btn border  btn-outline-primary btn-sm '>
                        <i class="fa fa-cogs" aria-hidden="true"></i>
                        Go To Blog Admin Panel</a>
                </p>
            </div>
        @endif --}}
        <div>
        </div>
</nav>
