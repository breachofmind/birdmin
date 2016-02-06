@extends('cms::layouts.input')

@section($input->uid)
    <input id="{{$input->uid}}"
           type="text"
           placeholder="{{$input->getPlaceholder('URL Slug')}}"
           ng-model="slugField"
           ng-init='slugField="{{$input->value}}"'
           name="{{$input->nameAttribute()}}"
           class="slug-input"
           value="{{$input->value}}">
@stop

