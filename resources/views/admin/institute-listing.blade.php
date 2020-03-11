@extends('admin.layout')
@section('header')
@endsection
@section('main-content')
    @if(!empty($institutes->all()))
    <table class="striped">
        @foreach($institutes as $institute)
            <tr>
                <td>{{$institute->name}}</td>
                <td><a href="/admin/edit-institute/{{$institute->id}}"><i class="material-icons">edit</i></a></td>
                <td><a href="/admin/list-courses/{{$institute->id}}"><i class="material-icons">subject</i></a></td>
            </tr>
        @endforeach
    </table>
    @else
        <div class="card-panel"><span class="red-text">No institutes found!</span></div>
    @endif
@endsection
@section('footer')
    <div class="fixed-action-btn">
        <a class="btn-floating waves-effect btn-large" href="{{route('admin.show-create.institute')}}">
            <i class="large material-icons">note_add</i>
        </a>
    </div>
@endsection
