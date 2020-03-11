@component('mail::message')
# Webinar Registration Underway!

Hey there!<br>
You have been registered for the webinar '{{$webinar->name}}' which commences on {{$webinar->starts_at}}.
Please make sure that any due fees related to the registration have been completed successfully to ensure that it is successfully validated.

Thanks,<br>
Yoken Online
@endcomponent
