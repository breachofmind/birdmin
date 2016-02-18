<div class="alert-box success">
    <p>Welcome, @{{state.user.first_name}}!</p>
</div>

@foreach(config('view.dashboard') as $class)

    {!! $class::create() !!}

@endforeach