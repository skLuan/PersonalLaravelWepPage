<nav id="footer">
    @if (\Auth::check() && \Auth::user()->canManageBinshopsBlogPosts())
        <div class="text-center">
            <p class='mb-1'>You are logged in as a blog admin user.
                <br>
                <a href='{{ route('binshopsblog.admin.index') }}'
                    class='btn border  btn-outline-primary btn-sm '>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    Go To Blog Admin Panel</a>
            </p>
        </div>
    @endif
    <div>
    </div>
</nav>