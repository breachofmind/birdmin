@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="number"
       placeholder="{{$input->getPlaceholder('Number value')}}"
       name="{{$input->nameAttribute()}}"
       value="{{$input->value}}">
@stop

