<div class="row">
    <div class="col m6 s12">
        @if(!$errors->isEmpty())
            <p><strong>Please resolve the following errors:</strong></p>
            <ul class="collection">
                @foreach($errors->all() as $error)
                    <li class="collection-item red lighten-2"><span class="white-text">{{$error}}</span></li>
                @endforeach
            </ul>
        @endif
    </div>
</div>