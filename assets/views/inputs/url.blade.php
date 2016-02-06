@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="url"
       placeholder="{{$input->getPlaceholder('http://')}}"
       name="{{$input->nameAttribute()}}"
       value="{{$input->value}}">
@stop

