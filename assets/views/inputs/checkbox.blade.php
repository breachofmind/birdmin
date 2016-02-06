@extends('cms::layouts.input')

@section($input->uid)
    @foreach ($input->getSelectionOptions() as $i=>$option)
        <label for="checkbox-{{$input->name}}-{{$i}}">
            <input id="checkbox-{{$input->name}}-{{$i}}"
                   type="checkbox"
                   {{$input->isSelected($option[0],'checked="checked"')}}
                   name="{{$input->nameAttribute()}}"
                   value="{{$option[0]}}"> {{$option[1]}}
        </label>
    @endforeach
@stop