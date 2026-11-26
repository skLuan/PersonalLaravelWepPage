<footer class="bg-skl-grey text-skl-white flex flex-col items-center border-t-2 border-skl-grey z-50 relative pb-12">
    <nav aria-label="Footer navigation" class="self-start w-full mx-auto max-w-10/12">
        <ul class="flex flex-row flex-wrap items-center justify-center w-full border-b-2 border-skl-black p-4">
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('home') ? 'active' : '' }}"
                    href="/" aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">Home</a>
            </li>
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('blog.*') ? 'active' : '' }}"
                    href="/blog">Blog</a>
            </li>
            <li class="p-1 mx-2">
                <a class="block text-lg no-underline font-bold text-skl-pink hover:text-skl-purple transition-all {{ request()->routeIs('portfolio') ? 'active' : '' }}"
                    href="/portfolio">Portfolio</a>
            </li>
        </ul>
    </nav>

    <section class="p-3 lg:flex lg:justify-evenly items-center w-full max-w-10/12 mx-auto">
        <p class="text-center text-base lg:text-left">{{ __('messages.footer.personal_message') }}</p>
        <div class="text-center lg:text-left lg:flex flex-row items-center justify-center">
            <h2 class="text-lg font-semibold p-0">{{ __('messages.footer.follow_me') }}</h2>
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
            {{ __('messages.footer.built_with') }} <a class="p-0" href="https://laravel.com" target="_blank" rel="noopener noreferrer"
                class="text-skl-pink">Laravel</a> {{ __('messages.footer.and') }}
            <a class="p-0" href="https://filamentphp.com" target="_blank" rel="noopener noreferrer"
                class="text-skl-pink">Filament</a>.
        </p>
        <p class="text-sm">© {{ date('Y') }} Luan Erazo. {{ __('messages.footer.all_rights_reserved') }}</p>
    </section>
    <section class="w-10/12 mx-auto">
        <p class="text-xs p-2 mx-auto text-justify">
            {{ __('messages.footer.privacy_text') }}
        </p>
    </section>
    <script type="application/ld+json">
  {
    "@@context": "https://schema.org",
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
