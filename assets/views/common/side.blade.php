<nav id="Side" ng-class="{collapse:state.collapse}">

    <ul class="navigation-list">
        <li ng-class="{active:state.url=='{{cms_url()}}'}" title="Home">
            <a href="{{cms_url()}}" rel="home" brd-link><i class="lnr-home3"></i> <span>Home</span></a>
        </li>
    </ul>


    @foreach ($modules->getNavigation() as $module=>$component)

        {!! $component->render() !!}

    @endforeach

</nav>