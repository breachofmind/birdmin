@extends('cms::layouts.input')

@section($input->uid)
    <?php $class = $input->class; ?>

    <select name="{{$input->nameAttribute()}}" id="{{$input->uid}}">

        @if($input->models && $input->isNullable())
            <option value="0">No {{$class::singular()}}</option>
        @endif

        @forelse($input->models as $model)

            <option value="{{$model->id}}"
                    {{$input->isSelected($model->id, 'selected="selected"')}}
                    {{$input->isDisabled($model, 'disabled')}}>
                {{$model->getTitle()}}
            </option>

        @empty

            <option value="0">No {{$class::plural()}} Found.</option>

        @endforelse
    </select>
@stop