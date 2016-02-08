<div ng-controller="FormController as form">

<h1 class="brd-title">Edit {{$class::singular()}}: <strong id="ModelTitle" ng-bind="titleField">{{ $model->getTitle() }}</strong></h1>

@include('cms::common.errors')

    <div class="row">
        <div class="small-12 medium-6 columns">
            <form id="{{$model->uid."Form"}}" action="{{$model->editUrl()}}" method="POST" enctype="multipart/form-data">

                <div class="input-form">
                    {!! csrf_field() !!}
                    {!! $model->inputs()->render() !!}
                </div>

            </form>
        </div>

        <div class="small-12 medium-6 columns">
            {!! $model->img() !!}
        </div>
    </div>
@foreach($model->getComponents() as $component)

    {!! $component->render() !!}

@endforeach

</div>