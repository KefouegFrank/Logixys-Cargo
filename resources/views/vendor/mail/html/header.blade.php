@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{-- asset() is absolute via APP_URL; a mail client has no base to resolve against. --}}
<img src="{{ asset(config('brand.lockup.light')) }}" class="logo" alt="{{ config('app.name') }}">
</a>
</td>
</tr>
