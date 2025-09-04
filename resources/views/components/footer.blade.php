<footer class="bg-skl-grey text-skl-white flex flex-col items-center border-t-2 border-skl-grey z-50 relative pb-12">
    <nav aria-label="Footer navigation">
        <ul class="flex flex-row flex-wrap items-center justify-center w-full border-b-2 border-skl-black p-4">
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('home') ? 'active' : '' }}"
                    href="/" aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">Home</a>
            </li>
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('binshopsblog.index') ? 'active' : '' }}"
                    href="/blog">Blog</a>
            </li>
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('portfolio') ? 'active' : '' }}"
                    href="/portfolio">Portfolio</a>
            </li>
        </ul>
    </nav>

    <section class="p-3 lg:flex lg:justify-evenly w-full">
        <p class="mb-4 text-center lg:text-left">This site is a personal project showcasing my work and skills.</p>
        <div class="text-center lg:text-left lg:flex flex-row items-center justify-center">
            <h2 class="text-lg font-semibold p-0">Follow me on</h2>
            <ul class="flex flex-row flex-wrap items-center justify-center" aria-label="Social media links">
                <li>
                    <a class="inline-flex" href="https://twitter.com/yourprofile" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow on Twitter">
                        <iconify-icon icon="simple-icons:medium" width="24" height="24"
                            aria-hidden="true"></iconify-icon>
                    </a>
                </li>
                <li>
                    <a class="inline-flex" href="https://github.com/yourprofile" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow on GitHub">
                        <iconify-icon icon="akar-icons:github-fill" width="24" height="24"
                            aria-hidden="true"></iconify-icon>
                    </a>
                </li>
                <li>
                    <a class="inline-flex" href="https://linkedin.com/in/yourprofile" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow on LinkedIn">
                        <iconify-icon icon="icomoon-free:linkedin" width="24" height="24"
                            aria-hidden="true"></iconify-icon>
                    </a>
                </li>
                <li>
                    <a class="inline-flex" href="https://instagram.com/yourprofile" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow on Instagram">
                        <iconify-icon icon="akar-icons:instagram-fill" width="24" height="24"
                            aria-hidden="true"></iconify-icon>
                    </a>
                </li>
                <li>
                    <a class="inline-flex" href="https://facebook.com/yourprofile" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow on Facebook">
                        <iconify-icon icon="akar-icons:facebook-fill" width="24" height="24"
                            aria-hidden="true"></iconify-icon>
                    </a>
                </li>
                <li>
                    <a class="inline-flex" href="mailto:youremail@example.com" aria-label="Send email">
                        <iconify-icon icon="akar-icons:mail-fill" width="24" height="24"
                            aria-hidden="true"></iconify-icon>
                    </a>
                </li>
            </ul>
        </div>
    </section>

    <section
        class="border-t border-skl-grey bg-skl-black w-full p-4 text-center lg:flex lg:flex-row justify-center items-end">
        <p class="text-sm">
            Built with <a class="p-0" href="https://laravel.com" target="_blank" rel="noopener noreferrer"
                class="text-skl-pink">Laravel</a> and
            <a class="p-0" href="https://binshops.com/laravel-blog-package" target="_blank" rel="noopener noreferrer"
                class="text-skl-pink">BinshopsBlog</a>.
        </p>
        <p class="text-sm">© {{ date('Y') }} Luan Erazo. All rights reserved.</p>
    </section>
    <section class="w-10/12 mx-auto">
        <p class="text-xs p-2 mx-auto text-justify">
            We use Microsoft Clarity and Google Analytics to analyze how you interact with our website, helping us
            improve user interactions and experiment with tools in a safe environment. By using our site, you agree that
            we, Microsoft, and Google can collect and use this data. Our privacy policy [link to your privacy policy]
            has more details.
        </p>
    </section>
    {{-- 
  @if (\Auth::check() && \Auth::user()->canManageBinshopsBlogPosts())
    <section class="p-4 text-center" aria-label="Admin controls">
      <p class="mb-2">You are logged in as a blog admin user.</p>
      <a href="{{ route('binshopsblog.admin.index') }}"
         class="inline-block px-4 py-2 text-sm font-semibold border border-skl-pink text-skl-pink hover:bg-skl-pink hover:text-skl-white transition-all"
         aria-label="Go to Blog Admin Panel">
        <i class="fa fa-cogs" aria-hidden="true"></i> Go To Blog Admin Panel
      </a>
    </section>
  @endif --}}

    <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Luan Erazo's Personal Site",
    "url": "https://luane.online",
    "author": {
      "@type": "Person",
      "name": "Luan Erazo"
    },
    "sameAs": [
      "https://twitter.com/yourprofile",
      "https://github.com/yourprofile",
      "https://linkedin.com/in/yourprofile",
      "https://instagram.com/yourprofile",
      "https://facebook.com/yourprofile"
    ]
  }
  </script>
</footer>
