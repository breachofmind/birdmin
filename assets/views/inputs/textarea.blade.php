@extends('cms::layouts.input')

@section($input->uid)
    <textarea name="{{$input->nameAttribute()}}"
              id="{{$input->uid}}">{{$input->value}}</textarea>
@stop