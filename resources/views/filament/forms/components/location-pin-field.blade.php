@php
    $locationPath = $field->getLocationStatePath();
    $statusPath = $field->getStatusStatePath();

    // Alpine reads these as raw JS, so they have to be built here rather than inline in
    // the component tag, where the nested quotes break Blade's tag parser.
    $bind = fn (string $path) => '$wire.'.$field->applyStateBindingModifiers("\$entangle('{$path}')");
@endphp

<x-shipment-map
    editable
    :state-expression="$bind($field->getStatePath())"
    :location-expression="$locationPath ? $bind($locationPath) : 'null'"
    :status-expression="$statusPath ? $bind($statusPath) : 'null'"
    :status-labels="$field->getStatusLabels()"
    :center-lat="$field->getCenterLat()"
    :center-lng="$field->getCenterLng()"
/>
