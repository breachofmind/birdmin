@extends('cms::layouts.input')

@section($input->uid)
<input id="{{$input->uid}}"
       type="text"
       placeholder="{{$input->getPlaceholder('Color value')}}"
       name="{{$input->nameAttribute()}}"
       class="jscolor"
       value="{{$input->value}}">

<script>
       (function(Swatch){
              var id = "{{$input->uid}}";
              new Swatch(id)
       })(jscolor)
</script>
@stop

