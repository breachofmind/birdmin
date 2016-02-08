<div class="related-models-component component">
    <h1><i class="lnr-{{$class::getIcon()}}"></i> {{$class::getLabel('navigation')}}</h1>

    <div class="action-row">
        {!! $actions !!}
    </div>

    <div class="related-objects">
        <ul>
            @foreach($related as $object)
                <li><a href="{{$object->editUrl()}}">{{$object->getTitle()}}</a></li>
                @endforeach
        </ul>
    </div>
</div>

