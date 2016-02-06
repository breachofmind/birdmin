<table ng-controller="tableController" style="width:100%">
    @yield('caption')
    <thead>
    @yield('header')
    </thead>

    <tbody>
    @yield('body')
    </tbody>

    @yield('footer')

</table>