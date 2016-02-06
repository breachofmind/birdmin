<div ng-controller="FormController as form">

<h1 class="brd-title">New {{ $class::singular() }} <strong>@{{titleField}}</strong></h1>

@include('cms::common.errors')

<form id="{{$class::singular(true)."Form"}}" action="{{ cms_url($class::plural()."/create") }}" method="POST" enctype="multipart/form-data">

    <div class="input-form">
        {!! csrf_field() !!}
        {!! $model->inputs->render() !!}
    </div>

</form>

</div>