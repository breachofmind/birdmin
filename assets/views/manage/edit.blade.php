<div ng-controller="FormController as form">

<h1 class="brd-title">Edit {{$class::singular()}}: <strong id="ModelTitle" ng-bind="titleField">{{ $model->getTitle() }}</strong></h1>

@include('cms::common.errors')

<form id="{{$model->uid."Form"}}" action="{{$model->editUrl()}}" method="POST" enctype="multipart/form-data">

    <div class="input-form">
        {!! csrf_field() !!}
        {!! $model->inputs()->render() !!}
    </div>

</form>


@foreach($model->getComponents() as $component)

    {!! $component->render() !!}

@endforeach

</div>