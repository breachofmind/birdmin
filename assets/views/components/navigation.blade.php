<li ng-class="{active:state.url.hasSegments('{{$href}}')}" title="{{$label}}">
    <a {!! $attributes !!}>
        @if ($icon)
            <i class="lnr-{{$icon}}"></i>
        @endif
        <span>{!! $label !!}</span>
    </a>
</li>

