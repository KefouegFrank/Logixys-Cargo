<x-mail::message>
# {{ __('contact.mail.internal_heading') }}

**{{ __('contact.form.name') }}:** {{ $contactMessage->name }}
**{{ __('contact.form.email') }}:** {{ $contactMessage->email }}
@if ($contactMessage->phone)
**{{ __('contact.form.phone') }}:** {{ $contactMessage->phone }}
@endif
**{{ __('contact.form.subject') }}:** {{ $contactMessage->subject->label() }}
**{{ __('contact.mail.locale') }}:** {{ strtoupper($contactMessage->locale) }}

<x-mail::panel>
{{ $contactMessage->message }}
</x-mail::panel>

<x-mail::button :url="route('filament.admin.resources.contact-messages.view', $contactMessage)">
{{ __('contact.mail.open_in_admin') }}
</x-mail::button>

{{ __('contact.mail.reply_hint') }}
</x-mail::message>
