<div class="related-media-component component">
    <h1><i class="lnr-{{$icon}}"></i> Media</h1>

    <div class="action-row">
        <a href="javascript:;" class="button success">Add Existing Media</a>
    </div>

    {!! $dropzone !!}

    <div id="{{$dropzoneId."List"}}" class="media-list row">

        @foreach($media as $item)

            <div class="media-list-item">
                {!! $item->img('sm') !!}
            </div>

        @endforeach
    </div>

    <script id="{{$dropzoneId."Template"}}" type="text/x-handlebars-template">
        <div class="media-list-item">
            <img src="@{{ url }}" alt="@{{ alt_text }}"/>
        </div>
    </script>
</div>

