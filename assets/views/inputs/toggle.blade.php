@extends('cms::layouts.input')

@section($input->uid)
    <div class="switch radius">
        <input id="toggle-{{$input->name}}"
               type="checkbox"
               name="{{$input->nameAttribute()}}"
               {{$input->isSelected("1",'checked="checked"')}}
               value="1">
        <label for="toggle-{{$input->name}}"></label>
    </div>
@stop