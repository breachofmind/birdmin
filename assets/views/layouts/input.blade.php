<div id="Input-{{$input->id}}"
     class="input-type--{{$input->type}} input-row {{$input->isTitleField('title-field')}}"

     data-priority="{{$input->priority}}">
    <div class="input-column input-label">
        <label for="{{$input->uid}}">{{$input->label}}</label>
    </div>
    <div class="input-column input-field">
        @yield($input->uid)
    </div>
</div>