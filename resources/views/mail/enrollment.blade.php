@component('mail::message')
# Enrollment Underway!

Hey there!<br>
You have been enrolled for the course '{{$course->name}}' at {{$institute->name}} which commences on {{$batch->commence_date}}.
Please make sure that any due fees related to the enrollment have been completed successfully to ensure that the enrollment is successfully validated by the institute.

Thanks,<br>
Yoken Online
@endcomponent
