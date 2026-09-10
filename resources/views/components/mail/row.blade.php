{{-- One label/value line in a details table. Skipped when there is nothing to show. --}}
@props(['label', 'strong' => false, 'color' => null])

@php $value = trim($slot->toHtml()); @endphp

@if ($value !== '')
    <tr>
        <td style="padding:7px 0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;color:#466285;vertical-align:top;width:45%;">
            {{ $label }}
        </td>
        <td style="padding:7px 0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;color:{{ $color ?? '#102946' }};vertical-align:top;{{ $strong ? 'font-weight:bold;' : '' }}">
            {!! $value !!}
        </td>
    </tr>
@endif
