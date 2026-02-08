@component('mail::message')
# প্রিয় গ্রাহক,

আপনার এসএমএস ব্যালেন্স: BDT {{ $balance }}

ধন্যবাদ,<br>
{{ config('app.name') }}
@endcomponent
