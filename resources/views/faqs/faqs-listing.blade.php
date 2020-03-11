@extends('institute.layout')
@section('header')
    <style>
        table {
            width: 100%;
        }
        td {
            max-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        td.question, th.question {
            width: 45%;
        }
        td.answer, th.question {
            width: 45%;
        }
    </style>
@endsection
@section('main-content')
    @if(!empty($faqs->all()))
    <table class="striped">
        <thead>
        <tr>
            <th class="question">Question</th>
            <th class="answer">Answer</th>
        </tr>
        </thead>
            @foreach($faqs as $faq)
                <tbody>
                <tr>
                    <td class="question"><strong>{{$faq->question}}</strong></td>
                    <td class="answer">{{$faq->answer}}</td>
                    <td>
                        <a href="{{route('faq.update', ['faq' => $faq->id])}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Edit"><i class="material-icons green-text text-lighten-2">mode_edit</i></a>
                    </td>
                    <td>
                        <form action="{{route('faq.delete', ['faq'=> $faq->id])}}" method="post">{{csrf_field()}}</form>
                        <a href="#" class="tooltipped delete-faq-button" data-position="bottom" data-delay="50" data-tooltip="Remove"><i class="material-icons red-text text-lighten-2">delete</i></a>
                    </td>
                </tr>
                </tbody>
            @endforeach
    </table>
    @else
        <div class="card-panel"><span class="red-text text-lighten-1">You have not added any FAQs added yet.</span></div>
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
        <a class="btn-floating waves-effect btn-large" href="{{route('faq.create', ['course' => $courseId])}}">
            <i class="large material-icons">add</i>
        </a>
    </div>
    <script>
        $(document).ready(function(){
            $('.modal').modal({dismissible: false});
            $('.delete-faq-button').click(function() {
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