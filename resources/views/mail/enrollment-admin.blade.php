@component('mail::message')
# Enrollment Made!

Hey there!<br>
We've received an enrollment for the course '{{$course->name}}({{$course->id}})' at {{$institute->name}}({{$institute->id}}) which commences on {{$batch->commence_date}}({{$batch->id}}).
The user is {{$user->name}}({{$user->id}}), whose phone number is {{$user->phone}} and email is {{$user->email}}

Thanks,<br>
Yoken Online
@endcomponent
