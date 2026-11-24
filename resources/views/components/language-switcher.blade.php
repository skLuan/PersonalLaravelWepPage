@props(['currentLocale' => null])

@php
    $currentLocale = $currentLocale ?? app()->getLocale();
    $availableLocales = config('app.available_locales', ['en']);
    $localeNames = [
        'en' => __('messages.lang.english'),
        'es' => __('messages.lang.spanish'),
    ];
@endphp

<div class="relative inline-block">
    <button id="language-dropdown-btn"
            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-skl-pink hover:text-skl-purple transition-all rounded-md focus:outline-none focus:ring-2 focus:ring-skl-purple"
            aria-haspopup="true"
            aria-expanded="false"
            aria-label="{{ __('messages.lang.switch_to', ['language' => '']) }}">
        <iconify-icon icon="tabler:language" class="w-5 h-5" aria-hidden="true"></iconify-icon>
        <span class="hidden sm:inline">{{ $localeNames[$currentLocale] ?? strtoupper($currentLocale) }}</span>
        <iconify-icon icon="tabler:chevron-down" class="w-4 h-4" aria-hidden="true"></iconify-icon>
    </button>

    <div id="language-dropdown-menu"
         class="absolute right-0 mt-2 w-32 bg-skl-white dark:bg-skl-black border border-skl-grey rounded-md shadow-lg opacity-0 invisible transform scale-95 transition-all duration-200 z-50"
         role="menu"
         aria-orientation="vertical"
         aria-labelledby="language-dropdown-btn">

        @foreach($availableLocales as $locale)
            @if($locale !== $currentLocale)
                <a href="{{ route('locale.switch', $locale) }}"
                   class="block px-4 py-2 text-sm text-skl-pink hover:text-skl-purple hover:bg-skl-grey-50 dark:hover:bg-skl-grey transition-colors"
                   role="menuitem"
                   lang="{{ $locale }}"
                   aria-label="{{ __('messages.lang.switch_to', ['language' => $localeNames[$locale]]) }}">
                    {{ $localeNames[$locale] }}
                </a>
            @endif
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('language-dropdown-btn');
    const dropdownMenu = document.getElementById('language-dropdown-menu');
    let isOpen = false;

    function toggleDropdown() {
        isOpen = !isOpen;
        if (isOpen) {
            dropdownMenu.classList.remove('opacity-0', 'invisible', 'scale-95');
            dropdownMenu.classList.add('opacity-100', 'visible', 'scale-100');
            dropdownBtn.setAttribute('aria-expanded', 'true');
        } else {
            dropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
            dropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
            dropdownBtn.setAttribute('aria-expanded', 'false');
        }
    }

    function closeDropdown() {
        if (isOpen) {
            toggleDropdown();
        }
    }

    // Toggle dropdown on button click
    dropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleDropdown();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            closeDropdown();
        }
    });

    // Close dropdown on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    // Handle language switch links
    dropdownMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const locale = this.getAttribute('href').split('/').pop();

            // Show loading state
            this.innerHTML = '<span class="flex items-center"><iconify-icon icon="tabler:loader" class="w-4 h-4 animate-spin mr-2"></iconify-icon>{{ __("messages.loading") }}</span>';

            // Redirect to new locale
            window.location.href = this.getAttribute('href');
        });
    });
});
</script>