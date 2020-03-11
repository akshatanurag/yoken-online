@extends('user.layout')

@section('main-content')
    <div class="container" id="year-wise-sales">
        <div class="row">
            <h3>Resource {{$resource->name}}</h3>
        </div>
        <div class="row">
            <h5>Description</h5>
            <div class="col m12">
                {{$resource->description}}
            </div>
        </div>
        <br />
        <hr />
        <br />
        <br />
        <div class="row">
            <div class="col m12">
                {!! $resource->embed_code !!}
            </div>
        </div>
    </div>
@endsection

@section('footer')
@endsection
