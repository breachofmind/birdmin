@if($count)
    @if($element)
        <?php echo "<$element ".attributize($attributes).">"; ?>
    @endif

    @foreach($buttons as $btn)

        {!! $btn->render() !!}

    @endforeach

    @if($element)
        <?php echo "</$element>"; ?>
    @endif
@endif
