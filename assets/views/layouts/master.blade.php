<!DOCTYPE HTML>
<html ng-app="birdmin">
<head>
    <title>{{ $template->title }}</title>

    {!! $template->head() !!}

</head>

<body ng-controller="BirdminController as app" class="@{{ template.bodyClass }}">


    <div id="Application">

        <header id="Header">
            Birdmin @{{url}}
        </header>


        <div id="Body">
            <nav id="Side">
                @yield('side')
            </nav>

            <section id="Content" ng-class="{processing:processing}">
                <div bind-unsafe-html="view"></div>
            </section>
        </div>

    </div>


</body>
</html>