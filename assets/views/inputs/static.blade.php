@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="text"
       readonly
       placeholder="{{$input->getPlaceholder('Text value')}}"
       name="{{$input->nameAttribute()}}"
       value="{{$input->value}}">
@stop

