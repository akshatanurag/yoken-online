@extends('admin.layout')
@section('header')
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
    @if(!$errors->isEmpty())
        <p><strong>Please resolve the following errors:</strong></p>
        <ul class="collection">
        @foreach($errors->all() as $error)
            <li class="collection-item red-text text-lighten-1">{{$error}}</li>
        @endforeach
        </ul>
    @endif
    <div class="row">
        <form class="col m7 s12" method="post" enctype="multipart/form-data" action="{{route('admin.update.institute')}}">
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="institute-name" type="text" class="validate" name="name" value="{{$institute->name}}">
                        <label for="institute-name">Institute Name</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <textarea id="institute-description" class="materialize-textarea" name="description">{{$institute->description}}</textarea>
                    <label for="institute-description">Description</label>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="city-name" type="text" class="validate" name="city" value="{{$institute->city}}">
                        <label for="city-name">City</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="state" type="text" class="validate" name="state" value="{{$institute->state}}">
                        <label for="state">State</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="locality" type="text" class="validate" name="location" value="{{$institute->location}}">
                        <label for="locality">Locality</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="email" type="text" class="validate" name="email" value="{{$institute->email}}" readonly="readonly">
                        <label for="email">Email</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="contact" type="text" class="validate" name="contact" value="{{$institute->contact}}">
                        <label for="contact">Contact</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="affiliation" type="text" class="validate" name="affiliation" value="{{$institute->affiliation}}">
                        <label for="affiliation">Affiliation</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="no-of-students" type="number" class="validate" name="no_of_students" value="{{$institute->no_of_students}}">
                        <label for="no-of-students">Number of students</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="file-field input-field" style="margin-top:50px">
                        <div class="btn">
                            <span>File</span>
                            <input type="file" name="logo_upload" id="logo-upload">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Upload institute logo">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="logo-container">
                        <img src="/storage/{{$institute->logo_file}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12 s12">
                    <div class="file-field input-field" style="margin-top:50px">
                        <div class="btn">
                            <span>File</span>
                            <input type="file" name="institution_display_pictures[]" id="institute-pics" multiple>
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Institution display pictures">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <div class="slider-container">
                        <div class="fotorama" data-nav="thumbs">
                            <?php
                            $links = $institute->display_pic_links;
                            $links = explode(';',$links);
                            ?>
                            @foreach($links as $link) {
                            <a href="{{  $link }}"><img src="{{$link}}" alt="institute-pic"></a>
                            @endforeach
                            }}
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <input type="submit" name="submit" class="btn waves-effect" value="Update">
            </div>
            {{csrf_field()}}
            <input type="hidden" name="instituteId" value="{{$institute->id}}">
        </form>
    </div>
@endsection

@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
@endsection