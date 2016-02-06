@extends('cms::layouts.input')

@section($input->uid)
    <input id="{{$input->uid}}"
           type="text"
           name="{{$input->nameAttribute()}}"
           placeholder="{{$input->getPlaceholder('Date')}}"
           class="datepicker"
           value="{{$input->value}}">
@stop

