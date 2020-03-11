@extends('institute.layout')
@section('header')
@endsection
@section('main-content')
    @include('partials.errors')
    <p><strong>Enter faq details:</strong></p>
    <form action="{{route('faq.store')}}" method="post">
        <div class="fields-container">
            <div class="field faq-template" hidden>
                <div class="row">
                    <div class="input-field col m5 s12">
                        <textarea class="materialize-textarea" name="question[]" placeholder="Enter question.." disabled></textarea>
                    </div>
                    <div class="input-field col m5 s12">
                        <textarea class="materialize-textarea" name="answer[]" placeholder="Enter answer.." disabled></textarea>
                    </div>
                    <div class="col m2 s12">
                        <a class="grey-text remove-field-btn" href="#"><i class="material-icons">close</i></a>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="row">
                    <div class="input-field col m5 s12">
                        <textarea class="materialize-textarea" name="question[]" placeholder="Enter question.."></textarea>
                    </div>
                    <div class="input-field col m5 s12">
                        <textarea class="materialize-textarea" name="answer[]" placeholder="Enter answer.."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <a href="#" id="add-more-btn" class="btn-flat waves-effect">Add more</a>
        {{csrf_field()}}
        <input type="hidden" name="courseId" value="{{$courseId}}">
        <input type="submit" class="btn waves-effect" value="Add faq(s)">
    </form>
@endsection
@section('footer')
    <script>
        $(document).ready(function() {
            $('#add-more-btn').click(function() {
                var field = $('.faq-template').clone();
                field.removeClass('faq-template');
                field.prop('hidden', false);
                field.appendTo('.fields-container');
                field.find('textarea').prop('disabled', false);
                $('.remove-field-btn').click(function() {
                    $(this).parent().parent().remove();
                });
            });
        });
    </script>
@endsection