@extends('cms::layouts.input')

@section($input->uid)
    <select name="{{$input->nameAttribute()}}" id="{{$input->uid}}">
        @foreach ($input->getSelectionOptions() as $i=>$option)
            <option value="{{$option[0]}}" {{$input->isSelected($option[0], 'selected="selected"')}}>{{$option[1]}}</option>
        @endforeach
    </select>
@stop