@extends('cms::layouts.input')

@section($input->uid)
    <textarea name="{{$input->nameAttribute()}}"
              id="{{$input->uid}}">
@if(!is_string($input->value))
{{stripslashes(json_encode($input->value))}}
@else
{{$input->value}}
@endif
</textarea>
@stop