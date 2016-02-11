@extends('cms::layouts.guest')

@section('content')

    <section id="LoginBlock">

        <div class="small-12 medium-centered medium-6 column">

            <img class="branding-logo" src="{{config('app.cms_logo')}}" alt="Birdmin"/>

            <form id="LoginForm" action="{!! cms_url('login') !!}" method="POST">
                @if ($request->input('landed'))
                    <div class="alert-box success">
                        <p>You were successfully logged out.</p>
                    </div>
                @endif

                @include('cms::common.errors')

                <div class="row">
                    <div class="small-3 columns">
                        <label class="bold">{{trans('cms.login.user')}}</label>
                    </div>
                    <div class="small-9 columns">
                        <input id="Username" type="email" placeholder="Email Address" name="email"/>
                    </div>
                </div>


                <div class="row">
                    <div class="small-3 columns">
                        <label class="bold">{{trans('cms.login.pass')}}</label>
                    </div>
                    <div class="small-9 columns">
                        <input id="Password" type="password" placeholder="Password" name="password"/>
                    </div>
                </div>


                <div class="text-right">
                    <label>
                        <input type="checkbox" name="remember"/>
                        {{trans('cms.login.remember')}}
                    </label>
                    <button type="submit">{{trans('cms.login.submit')}}</button>
                </div>


                {!! csrf_field() !!}
            </form>

            <div class="text-right version">
                <p>Birdmin <span>v.{{\Birdmin\Core\Application::VERSION}}</span>,
                    Laravel <span>v.{{\Illuminate\Foundation\Application::VERSION}}</span>
                </p>
            </div>
        </div>
    </section>

@endsection