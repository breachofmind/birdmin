@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="file"
       placeholder="{{$input->getPlaceholder('Select File')}}"
       name="{{$input->nameAttribute()}}"
       value="{{$input->value}}">
@stop

