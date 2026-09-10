{{--
    Table-based email shell. Everything is inlined because Gmail strips <style> blocks.
    The wordmark is the real logo asset, loaded from asset() — which is APP_URL, so it
    renders broken until that points at the live domain rather than localhost.
--}}
@props(['preheader' => null, 'title' => null])

@php
    $navy = '#102946';
    $gold = '#F9D52A';
    $ink = '#102946';
    $muted = '#466285';
    $line = '#C7D6E8';
    $page = '#F0F6FD';
    $font = "'Helvetica Neue', Helvetica, Arial, sans-serif";
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:{{ $page }};font-family:{{ $font }};-webkit-font-smoothing:antialiased;">
    @if ($preheader)
        {{-- Preview line in the inbox list; never rendered in the body itself. --}}
        <div style="display:none;font-size:1px;color:{{ $page }};line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
            {{ $preheader }}
        </div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:{{ $page }};">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(16,41,70,0.08);">
                    <tr>
                        <td align="center" style="background-color:{{ $navy }};padding:22px 24px;">
                            <img
                                src="{{ asset(config('brand.lockup.light')) }}"
                                width="180" height="45" alt="{{ config('app.name') }}"
                                style="display:block;border:0;outline:none;text-decoration:none;height:45px;width:180px;font-family:{{ $font }};font-size:16px;font-weight:bold;color:#ffffff;"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td style="height:4px;background-color:{{ $gold }};line-height:4px;font-size:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:28px 28px 8px 28px;font-family:{{ $font }};font-size:15px;line-height:1.6;color:{{ $ink }};">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 28px;">
                            <div style="border-top:1px solid {{ $line }};margin:20px 0 0 0;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px 28px 28px;font-family:{{ $font }};font-size:12px;line-height:1.6;color:{{ $muted }};">
                            <strong style="color:{{ $ink }};">{{ config('app.name') }}</strong><br>
                            {{ config('brand.contact.phone') }} &middot;
                            <a href="mailto:{{ config('brand.contact.email') }}" style="color:{{ $muted }};text-decoration:underline;">{{ config('brand.contact.email') }}</a><br>
                            <span style="color:{{ $muted }};">{{ __('notifications.layout.tagline') }}</span>
                            <div style="margin-top:14px;color:#849EBD;font-size:11px;">
                                {{ __('notifications.layout.auto') }}<br>
                                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('footer.rights') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
