<x-mail::message>
# {{ __('contact.mail.ack_heading', ['name' => $contactMessage->name]) }}

{{ __('contact.mail.ack_body') }}

<x-mail::panel>
{{ $contactMessage->message }}
</x-mail::panel>

{{ __('contact.mail.ack_signoff') }}

{{ config('app.name') }}
{{ config('brand.contact.phone') }} · {{ config('brand.contact.email') }}
</x-mail::message>
