@extends('institute.layout')
@section('header')
@endsection
@section('main-content')
    @if(!empty($installments->all()))
    <table class="striped centered">
        <thead>
        <tr>
            <th>Frequency</th>
            <th>Amounts (in Rupees)</th>
            <th>Duration from start date (in months)</th>
        </tr>
        </thead>
            @foreach($installments as $installment)
                <tbody>
                <tr>
                    <td>{{$installment->frequency}}</td>
                    <td>{{implode(' | ',explode(';', $installment->amounts))}}</td>
                    <td>{{implode(' | ',explode(';', $installment->payment_duration))}}</td>
                    <td>
                        <form action="{{route('installment.delete', ['installment'=> $installment->id])}}" method="post">{{csrf_field()}}</form>
                        <a href="#" class="tooltipped delete-installment-button" data-position="bottom" data-delay="50" data-tooltip="Remove"><i class="material-icons red-text text-lighten-2">delete</i></a>
                    </td>
                </tr>
                </tbody>
            @endforeach
    </table>
    @else
        <div class="card-panel"><span class="red-text text-lighten-1">You have not added any installments added yet.</span></div>
    @endif
    <div id="confirm-delete-modal" class="modal">
        <div class="modal-content">
            <h5>Warning!</h5>
            <p>Delete this batch? This cannot be undone!</p>
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
        <a class="btn-floating waves-effect btn-large" href="{{route('installment.create', ['course' => $courseId])}}">
            <i class="large material-icons">add</i>
        </a>
    </div>
    <script>
        $(document).ready(function(){
            $('.modal').modal({dismissible: false});
            $('.delete-installment-button').click(function() {
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