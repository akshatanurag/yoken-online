@component('mail::message')
# Institution Registration

Hey there!
You have successfully been registered as an institute at Yoken Online! Below are your institute login details:<br />
Email: {{$institute->email}}<br />
Password: {{$password}}<br />

Thanks,<br>
Yoken Online
@endcomponent
