

<h1>Media list</h1>

{{$parent->getTitle()}}

<div class="media-list">
    @foreach($media as $item)

        <div class="media-list-item">
            {!! $item->img('sm') !!}
        </div>

    @endforeach
</div>
