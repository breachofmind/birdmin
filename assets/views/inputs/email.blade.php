@extends('cms::layouts.input')

@section($input->uid)
    <input id="{{$input->uid}}"
           type="email"
           @if($input->isTitleField())
           ng-model="titleField"
           ng-init='titleField="{{$input->value}}"'
           @endif
           name="{{$input->nameAttribute()}}"
           value="{{$input->value}}"
           placeholder="{{$input->getPlaceholder('bird@flyer.com')}}">
@stop

