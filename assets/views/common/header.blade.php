<header id="Header" class="flex-row">
    <div id="Brand">
        <img class="branding-logo" src="{{config('app.cms_logo')}}" alt="Birdmin"/>
    </div>

    <div id="SessionUser">
        <div class="flex-row">
            <div class="session-user-actions">
                <a href="{{$user->editUrl()}}" brd-link><span>{{$user->fullName()}}</span> <i class="lnr-user"></i></a>
                <a href="{{cms_url('logout')}}"><span>Log out</span> <i class="lnr-power-switch"></i></a>
            </div>
            <div class="session-user-image">
                {!! $user->img('sm') !!}
            </div>
        </div>
    </div>
</header>