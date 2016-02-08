<form id="{{$id}}" action="{{$action}}" class="dropzone" enctype="multipart/form-data" data-handler="{{$handler}}">
    {!! csrf_field() !!}
    @foreach($relateTo as $model)
        <input type="hidden" name="relate[]" value="{{$model->objectName}}">
    @endforeach
</form>

<script id="DropzonePreviewTemplate" type="text/x-handlebars-template">
    <div class="dz-preview dz-file-preview dz-birdmin">
        <div class="dz-progress">
            <span class="dz-upload" data-dz-uploadprogress></span>
            <div class="dz-details">
                <div class="dz-filename"><span data-dz-name></span></div>
                <div class="dz-size" data-dz-size></div>
            </div>
            <div class="dz-success-mark"><i class="lnr-checkmark-circle"></i></div>
            <div class="dz-error-mark"><i class="lnr-cross-circle"></i></div>
        </div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
    </div>
</script>

<script>
    birdmin.ui.createDropzone("{{$id}}");
</script>