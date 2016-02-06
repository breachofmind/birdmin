@extends('cms::layouts.input')

@section($input->uid)
    @foreach ($input->getSelectionOptions() as $i=>$option)
        <label for="radio-{{$input->name}}-{{$i}}">
            <input id="radio-{{$input->name}}-{{$i}}"
                   type="radio"
                   {{$input->isSelected($option[0],'checked="checked"')}}
                   name="{{$input->nameAttribute()}}"
                   value="{{$option[0]}}"> {{$option[1]}}
        </label>
    @endforeach
@stop