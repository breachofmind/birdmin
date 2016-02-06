<?php
/**
 * Application frame.
 * All birdmin controllers using angular should deliver this template.
 */
?>
<!DOCTYPE HTML>
<html ng-app="birdmin" ng-controller="BirdminController as app" >
<head>
    <title ng-bind-html="template.title">Birdmin</title>

    {!! $template->head() !!}

</head>

<body class="@{{ template.body }}">
<div id="Application" class="flex-col">

    @include('cms::common.messaging')



    @include('cms::common.header')


    <div id="Body" class="flex-row">


        @include('cms::common.side')



        <main id="Content" ng-class="{processing:state.processing, loading:state.loading}" class="flex-row">

            <nav id="PageTypeNavigation" bind-unsafe-html="state.views"></nav>

            <div id="Page">

                <nav id="ActionNavigation">
                    <button-action-group buttons="state.actions.buttons" />
                </nav>

                <div id="Viewport">
                    @include('cms::common.errors')
                    <div id="View" bind-unsafe-html="state.view"></div>
                </div>


            </div>


            @include('cms::common.preloader')

        </main>

    </div>

</div>


</body>
</html>