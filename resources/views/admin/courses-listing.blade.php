@extends('admin.layout')
@section('header')
    <style>
        td {
            -ms-text-overflow: ellipsis;
            text-overflow: ellipsis;
        }
    </style>
@endsection
@section('main-content')
    <table class="striped">
    @if(!empty($courses))
    @foreach($courses as $course)
        <tr>
            <td width="50%"><a href="{{route('course.update', ['course'=> $course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="View courses details">{{$course->name}}</a></td>
            <td><a target="_blank" href="/admin/preview-course/{{$course->id}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Preview course"><i class="material-icons grey-text">visibility</i></a></td>
            <td><a href="/admin/batches/{{$course->id}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit batches"><i class="material-icons grey-text">people_outline</i></a></td>
            <td><a href="{{route('faculty.view', ['course'=>$course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit faculty"><i class="material-icons grey-text">school</i></a></td>
        </tr>
    @endforeach
    @else
        <li class="collection-item"><span class="red-text text-lighten-1">You have not added any courses added yet.</span></li>
    @endif
    </table>

@endsection
@section('footer')
@endsection