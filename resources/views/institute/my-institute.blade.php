@extends('institute.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.min.css">
    <style>
        .logo-container {
            width: 200px;
        }
        .logo-container img{
            width: 100%
        }
    </style>
@endsection
@section('main-content')
    <div class="container">
        <div class="row">
            <div class="col m6 s6">
                <strong>Logo</strong>
            </div>
            <div class="col m6 s6">
                <div class="logo-container">
                    <img src="/{{ str_replace(storage_path() . '/app/public', 'storage', $institute->logo_file) }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>Display pictures</strong>
            </div>
            <div class="col m6 s6">
                <div class="slider-container">
                    <div class="fotorama" data-nav="thumbs">
                        <?php
                        $links = $institute->display_pic_links;
                        $links = explode(';',$links);
                        ?>
                        @foreach($links as $link)
                            <a href="/{{ str_replace(storage_path() . '/app/public', 'storage', $link)}}"><img src="/{{ str_replace(storage_path() . '/app/public', 'storage', $link)}}" alt="institute-pic"></a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>Email</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->email}}
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>Description</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->description}}
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>State</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->state}}
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>City</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->city}}
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>Location</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->location}}
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>Contact</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->contact}}
            </div>
        </div>
        <div class="row">
            <div class="col m6 s6">
                <strong>Affiliation</strong>
            </div>
            <div class="col m6 s6">
                {{$institute->affiliation}}
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <div class="fixed-action-btn">
        <a class="btn-floating waves-effect btn-large" href="edit-institute">
            <i class="large material-icons">mode_edit</i>
        </a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.min.js"></script>
@endsection