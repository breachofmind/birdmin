@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="text"
       @if($input->isTitleField())
       ng-model="titleField"
       ng-init='titleField="{{$input->value}}"'
       ng-change="slugify()"
       @endif
       placeholder="{{$input->getPlaceholder('Text value')}}"
       name="{{$input->nameAttribute()}}"
       value="{{$input->value}}">
@stop

