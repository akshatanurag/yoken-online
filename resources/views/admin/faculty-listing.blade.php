@extends('admin.layout')
@section('header')
@endsection
@section('main-content')
    <table class="striped">
        @if(!empty($faculties))
            @foreach($faculties as $faculty)
                <tr>
                    <td width="80%">
                        <?php
                        $url = str_replace(storage_path() . '/app/public',url('/') . '/storage',$faculty->pic_link);
                        ?>
                        <img width="40" height="40" src="{{$url}}" alt="" class="circle responsive-img">
                        &nbsp;&nbsp;&nbsp;<span style="position:relative;bottom: 12px">{{$faculty->name}}</span>
                    </td>
                    <td>
                        <a href="/admin/edit-faculty/{{$faculty->id}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit"><i class="material-icons green-text text-lighten-2">mode_edit</i></a>
                    </td>
                    <td>
                        <form action="{{route('faculty.delete', ['faculty'=> $faculty->id])}}" method="post">{{csrf_field()}}</form>
                        <a href="#" class="tooltipped delete-faculty-button" data-position="bottom" data-delay="50" data-tooltip="Remove"><i class="material-icons red-text text-lighten-2">delete</i></a>
                    </td>
                </tr>
            @endforeach
        @else
            <span class="red-text text-lighten-1">You have not added any courses added yet.</span>
        @endif
    </table>
    <div id="confirm-delete-modal" class="modal">
        <div class="modal-content">
            <h5>Warning!</h5>
            <p>Delete this faculty? This cannot be undone!</p>
        </div>
        <div class="modal-footer">
            <a href="#!" id="confirm-delete-button" class="modal-action modal-close waves-effect white-text red lighten-2 btn-flat">Delete</a>
            &nbsp;&nbsp;
            <a href="#!" id="cancel-button" class="modal-action modal-close waves-effect btn-flat">Cancel</a>
        </div>
    </div>
@endsection
@section('footer')
    <div class="fixed-action-btn">
        <a class="btn-floating waves-effect btn-large" href="{{route('faculty.create', ['course' => $courseId])}}">
            <i class="large material-icons">add</i>
        </a>
    </div>
    <script>
        $(document).ready(function(){
            $('.modal').modal({dismissible: false});
            $('.delete-faculty-button').click(function() {
                var btn = $(this);
                $('#confirm-delete-modal').modal('open');
                $('#confirm-delete-button').click(function(){
                    btn.siblings('form').submit();
                });
                $('#cancel-button').click(function(){
                    $('#confirm-delete-modal').modal('close');
                });
            });
        });
    </script>
@endsection