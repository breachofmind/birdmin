@if (count($errors) > 0)
    <div data-alert class="alert-box alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <a href="#" class="close">&times;</a>
    </div>
@endif