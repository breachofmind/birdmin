<h1>Upload Media</h1>

<form id="MediaDropzone" action="{{cms_url('media/upload')}}" class="dropzone" enctype="multipart/form-data">
    {!! csrf_field() !!}
</form>

<script id="MediaDropzoneTemplate" type="text/x-handlebars-template">
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
    new Dropzone('#MediaDropzone');
</script>