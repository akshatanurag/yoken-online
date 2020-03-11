@extends('institute.layout')
@section('header')
@endsection
@section('main-content')
@include('partials.errors')
<form action="{{route('faq.update.store')}}" method="post">
    <div class="fields-container">
        <div class="field">
            <div class="row">
                <div class="input-field col m5 s12">
                    <textarea class="materialize-textarea" name="question" placeholder="Enter question..">{{$faq->question}}</textarea>
                </div>
                <div class="input-field col m5 s12">
                    <textarea class="materialize-textarea" name="answer" placeholder="Enter answer..">{{$faq->answer}}</textarea>
                </div>
            </div>
        </div>
    </div>
    {{csrf_field()}}
    <input type="hidden" name="faqId" value="{{$faq->id}}">
    <input type="submit" class="btn waves-effect" value="Add faq(s)">
</form>
@endsection
@section('footer')
@endsection