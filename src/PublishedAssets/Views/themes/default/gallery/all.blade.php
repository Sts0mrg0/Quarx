@extends('quarx-frontend::layout.master')

@section('content')

    <h1>Gallery</h1>

    <div class="col-md-8">
        @foreach ($images as $image)
            <img alt="{{ $image->alt_tag }}" src="{{ $image->url }}" />
        @endforeach
    </div>
    <div class="col-md-4">
        @foreach($tags as $tag)
            <a href="{{ url('gallery/'.$tag) }}" class="btn btn-default">{{ $tag }}</a>
        @endforeach
    </div>

@endsection

@section('quarx')
    {!! Quarx::editBtn('images') !!}
@endsection