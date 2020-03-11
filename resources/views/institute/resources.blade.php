@extends('institute.layout')
@section('main-content')
    @if($resources->count() > 0)
    <table class="striped">
        @foreach($resources as $resource)
            <tr>
                <td>{{$resource->name}}</td>
                @if($resource->course)
                    <td>Attached Course: {{$resource->course->name}}</td>
                @else
                    <td>No attached course</td>
                @endif
                @if($resource->expiry)
                    <td>{{$resource->expiry}}</td>
                @else
                    <td>No expiry</td>
                @endif
                <td><a href="/institute/resources/edit/{{$resource->id}}"><i class="material-icons">edit</i></a></td>
                <td>
                    <form action="{{route('institute.resources-delete', $resource->id)}}" method="post">{{csrf_field()}}</form>
                    <a href="#" class="tooltipped delete-resource-button" data-position="bottom" data-delay="50" data-tooltip="Remove"><i class="material-icons red-text text-lighten-2">delete</i></a>
                </td>
            </tr>
        @endforeach
    </table>
    @else
        <div class="card-panel"><span class="red-text">No resources found!</span></div>
    @endif
    <div id="confirm-delete-modal" class="modal">
        <div class="modal-content">
            <h5>Warning!</h5>
            <p>Delete this resource? This cannot be undone!</p>
        </div>
        <div class="modal-footer">
            <a href="#!" id="confirm-delete-button" class="modal-action modal-close waves-effect white-text red lighten-2 btn-flat">Delete</a>  &nbsp;&nbsp;
            <a href="#!" id="cancel-button" class="modal-action modal-close waves-effect btn-flat">Cancel</a>
        </div>
    </div>
@endsection
@section('footer')
    <div class="fixed-action-btn">
        <a class="btn-floating waves-effect btn-large" href="{{route('institute.resources-create')}}">
            <i class="large material-icons">note_add</i>
        </a>
    </div>
    <script>
        $(document).ready(function(){
            $('.modal').modal({dismissible: false});
            $('.delete-resource-button').click(function() {
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
