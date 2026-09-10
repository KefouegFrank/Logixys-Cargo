{{-- Centred CTA. Table-wrapped so Outlook keeps the padding. --}}
@props(['url'])

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:26px 0 8px 0;">
    <tr>
        <td align="center">
            <a href="{{ $url }}" style="display:inline-block;background-color:#F9D52A;color:#102946;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:15px;font-weight:bold;text-decoration:none;padding:13px 30px;border-radius:8px;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
