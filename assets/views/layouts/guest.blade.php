<!DOCTYPE HTML>
<html>
<head>
    <title>{{ $template->title }}</title>

    {!! $template->head() !!}

</head>

<body class="{{$template->bodyClass}}">
    <div class="row">
        @yield('content')
    </div>
</body>
</html>