@extends('cms::layouts.input')

@section($input->uid)
    <input id="{{$input->uid}}"
           type="text"
           placeholder="{{$input->getPlaceholder('Hashmap')}}"
           name="{{$input->nameAttribute()}}"
           class="hash-input"
           value="{{$input->value}}">
@stop

