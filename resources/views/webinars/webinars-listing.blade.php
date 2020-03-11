@extends('admin.layout')
@section('header')
@endsection
@section('main-content')
    @if(!empty($webinars->all()))
        <table class="striped">
            @foreach($webinars as $webinar)
                <tr>
                    <td>{{$webinar->name}}</td>
                    <td>
                        <a href="{{route('webinar.update.show', ['webinar' => $webinar->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit"><i class="material-icons green-text text-lighten-2">edit</i></a>
                    </td>
                    <td>
                        <form action="{{route('webinar.delete', ['webinar'=> $webinar->id])}}" method="post">{{csrf_field()}}</form>
                        <a href="#" class="tooltipped delete-webinar-button" data-position="bottom" data-delay="50" data-tooltip="Remove"><i class="material-icons red-text text-lighten-2">delete</i></a>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="card-panel"><span class="red-text">No webinars found!</span></div>
    @endif
    <div id="confirm-delete-modal" class="modal">
        <div class="modal-content">
            <h5>Warning!</h5>
            <p>Delete this webinar? This cannot be undone!</p>
        </div>
        <div class="modal-footer">
            <a href="#!" id="confirm-delete-button" class="modal-action modal-close waves-effect white-text red lighten-2 btn-flat">Delete</a>  &nbsp;&nbsp;
            <a href="#!" id="cancel-button" class="modal-action modal-close waves-effect btn-flat">Cancel</a>
        </div>
    </div>
@endsection
@section('footer')
    <div class="fixed-action-btn">
        <a class="btn-floating waves-effect btn-large" href="{{route('webinar.create')}}">
            <i class="large material-icons">note_add</i>
        </a>
    </div>
    <script>
        $(document).ready(function(){
            $('.modal').modal({dismissible: false});
            $('.delete-webinar-button').click(function() {
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