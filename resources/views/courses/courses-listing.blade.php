@extends('institute.layout')
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
            <td width="50%"><a href="{{route('course.edit.view', ['course'=> $course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="View courses details">{{$course->name}}</a></td>
            <td>
                    <div class="switch">
                        <label>
                            @if($course->status == 1)
                                <form style="display: none;" action="{{route('course.deactivate', ['course'=>$course->id])}}" method="post">{{csrf_field()}}</form>
                                <input class="course-toggle-switch" type="checkbox" checked="checked">
                            @else
                                <form style="display: none;" action="{{route('course.activate', ['course'=>$course->id])}}" method="post">{{csrf_field()}}</form>
                                <input class="course-toggle-switch" type="checkbox">
                            @endif
                                <span class="lever"></span>
                        </label>
                    </div>
            </td>
            <td><a target="_blank" href="{{route('course.preview', ['course'=>$course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Preview course"><i class="material-icons grey-text">visibility</i></a></td>
            <td><a href="{{route('batch.view', ['course'=>$course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit batches"><i class="material-icons grey-text">people_outline</i></a></td>
            <td><a href="{{route('faq.view', ['course'=>$course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit FAQs"><i class="material-icons grey-text">toc</i></a></td>
            <td><a href="{{route('faculty.view', ['course'=>$course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit faculty"><i class="material-icons grey-text">school</i></a></td>
            <td><a href="{{route('installment.view', ['course'=>$course->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit installment"><span class="grey-text" style="font-size: 20px;">&#8377</span></a></td>
        </tr>
    @endforeach
    @else
        <li class="collection-item"><span class="red-text text-lighten-1">You have not added any courses added yet.</span></li>
    @endif
    </table>
    <div id="confirm-activate-modal" class="modal">
        <div class="modal-content">
            <h5>Kindly review your updates</h5>
            <p>
                If you activate this course, all details of the course including batch, faculty and installment information
            will be sent to the administrator for review. Please review all of the above details before activating the course.
                You cannot deactivate the course later.
            </p>
        </div>
        <div class="modal-footer">
            <a href="#!" id="confirm-activate-button" class="modal-action modal-close waves-effect white-text red lighten-2 btn-flat">Activate</a>  &nbsp;&nbsp;
            <a href="#!" id="cancel-button" class="modal-action modal-close waves-effect btn-flat">Cancel</a>
        </div>
    </div>
    <div id="confirm-deactivate-modal" class="modal">
        <div class="modal-content">
            <h5>Kindly review your updates</h5>
            <p>
                If you deactivate this course, students will not be able to register for it. Are you sure you wish to deactivate it?
            </p>
        </div>
        <div class="modal-footer">
            <a href="#!" id="confirm-deactivate-button" class="modal-action modal-close waves-effect white-text red lighten-2 btn-flat">Deactivate</a>  &nbsp;&nbsp;
            <a href="#!" id="cancel-button" class="modal-action modal-close waves-effect btn-flat">Cancel</a>
        </div>
    </div>
@endsection
@section('footer')
    <div class="fixed-action-btn">
        <a class="btn-floating waves-effect btn-large" href="{{route('course.create')}}">
            <i class="large material-icons">note_add</i>
        </a>
    </div>
    <script>
        $('.modal').modal({dismissible: false});
        $('.course-toggle-switch').change(function () {
            var btn = $(this);
            if(btn.is(':checked')) {
                $('#confirm-activate-modal').modal('open');
                $('#confirm-activate-button').click(function(){
                    btn.siblings('form').submit();
                });
                $('#cancel-button').click(function(){
                    $('#confirm-activate-modal').modal('close');
                    btn.prop('checked', false);
                });
            }
            else {
                $('#confirm-deactivate-modal').modal('open');
                $('#confirm-deactivate-button').click(function(){
                    btn.siblings('form').submit();
                });
                $('#cancel-button').click(function(){
                    $('#confirm-deactivate-modal').modal('close');
                    btn.prop('checked', true);
                });
            }
        });
    </script>
@endsection
