@component('mail::message')
# Dear {{ $operator->name }}

Thank you for registering for the ISP Billing Software.

@component('mail::panel')
For the demo of the billing software please visit the demo link
@endcomponent

@component('mail::button', ['url' => "$demo_link", 'color' => 'primary'])
DEMO LINK
@endcomponent

@component('mail::button', ['url' => "https://docs.ispbills.com/", 'color' => 'primary'])
User Documentation
@endcomponent

@component('mail::panel')
If you are using this software, please confirm by clicking the confirmation link
@endcomponent

@component('mail::button', ['url' => "$confirmation_link", 'color' => 'success'])
I am using this software
@endcomponent

@component('mail::panel')
If you are not interested, you can delete your account information by clicking the delete link
@endcomponent

@component('mail::button', ['url' => "$delete_link", 'color' => 'error'])
Delete my account
@endcomponent

If you require any further information, feel free to contact me.

{{ config('consumer.sales_contact') }}

{{ $warning }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
