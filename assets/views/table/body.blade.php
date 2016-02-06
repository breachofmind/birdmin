@extends('cms::table.layout')

@section('header')
    <tr>
        @foreach ($table->getOrder() as $field=>$priority)
            <td>{!! $table->getHeader($field) !!}</td>
        @endforeach
    </tr>
@stop


@section('body')
    @forelse ($table->getItems() as $model)
    <tr data-uid="{{$model->uid}}">
        @foreach ($table->getOrder() as $field=>$priority)
            <td>{!! $table->getCell($model,$field) !!}</td>
        @endforeach
    </tr>

    @empty
    <tr>
        <td class="text-center" colspan="{{$table->totalColumns()}}">No results found.</td>
    </tr>

    @endforelse
@stop

@section('footer')
    <tfoot>

    </tfoot>
@stop