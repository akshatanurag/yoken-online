@component('mail::message')
# Registration Made!

Hey there!<br>
We've received a registration for the webinar '{{$webinar->name}}({{$webinar->id}})' which commences on {{$webinar->starts_at}}.
The user is {{$user->name}}({{$user->id}}), whose phone number is {{$user->phone}} and email is {{$user->email}}

Thanks,<br>
Yoken Online
@endcomponent
