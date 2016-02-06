@extends('cms::layouts.guest')

@section('content')

    <section id="LoginBlock">

        <div class="small-12 medium-centered medium-6 column">

            @if (isset($_GET['landed']))
            <div class="alert-box success">
                <p>You were successfully logged out.</p>
            </div>
            @endif

        <form id="LoginForm" action="{!! cms_url('login') !!}" method="POST">
            {!! csrf_field() !!}
            <label>
                Flyer ID
                <input id="Username" type="email" placeholder="Email Address" name="email"/>
            </label>

            <label>
                Clearance
                <input id="Password" type="password" placeholder="Password" name="password"/>
            </label>

            <label>

                <input type="checkbox" name="remember"/>
                I fly here often
            </label>

            <button type="submit">Fly</button>

        </form>
        </div>
    </section>

@endsection