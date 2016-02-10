@extends('cms::layouts.input')

@section($input->uid)
    <textarea id="froala{{$input->uid}}"
              class="froala-editor html-editor fr-view"
              name="{{$input->nameAttribute()}}">
        {{$input->value}}
    </textarea>
@stop