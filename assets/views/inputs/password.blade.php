@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="password"
       placeholder="{{$input->getPlaceholder('Password')}}"
       name="{{$input->nameAttribute()}}"
       value="">
@stop

