@extends('cms::layouts.input')

@section($input->uid)
    <textarea name="{{$input->nameAttribute()}}"
              placeholder="{{$input->getPlaceholder()}}"
              class="html-editor"
              id="{{$input->uid}}">{{$input->value}}</textarea>
@stop