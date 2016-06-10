<div ng-controller="FormController as form">

<h1 class="brd-title">New {{ $class::singular() }} <strong>@{{titleField}}</strong></h1>

@include('cms::common.errors')

<form id="{{$class::getLabel('slug')."Form"}}" action="{{ cms_url($class::getLabel('slug')."/create") }}" method="POST" enctype="multipart/form-data">

    <div class="input-form">
        {!! csrf_field() !!}
        {!! $model->inputs->render() !!}
    </div>

</form>

</div>