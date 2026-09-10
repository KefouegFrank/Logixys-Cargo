<?php

return [
    'layout' => [
        'tagline' => 'Your trusted logistics partner',
        'auto' => 'Sent automatically when your shipment was updated.',
    ],
    'shipment' => [
        // Requested verbatim by the client; kept as one line for every
        // notice kind rather than the created/status/updated variants it replaced.
        'subject' => 'Logixys Cargo Shipment Notification # :tracking.',
        'headline' => [
            'created' => 'Your shipment is registered',
            'updated' => 'Details updated',
        ],
        'line' => [
            'created' => 'We have registered your shipment. You will hear from us at each step, and you can track it any time with the number below.',
            'updated' => 'The details of your shipment have changed. Here is where things stand.',
        ],
        'greeting' => 'Hello :name,',
        'intro_shipper' => 'Here is where shipment :tracking, which you entrusted to us, stands.',
        'intro_receiver' => 'Here is where shipment :tracking, addressed to you, stands.',
        'details_heading' => 'Shipment details',
        'service' => 'Service type',
        'carrier' => 'Carrier',
        'goods' => 'Goods',
        'packages' => 'Packages',
        'support' => 'A question? Just reply to this email and our team will get back to you.',
        'tracking' => 'Tracking number',
        'route' => 'Route',
        'status' => 'Status',
        'date' => 'Date',
        'location' => 'Location',
        'eta' => 'Expected delivery',
        'track' => 'Track my shipment',
        'signoff' => 'Thank you for your trust,',
        'auto' => 'Automatic message. Reply to this email to reach our team.',
        'lines' => [
            'PENDING' => 'Your shipment is registered. We are arranging collection.',
            'PICKED_UP' => 'Your shipment has been collected by our team.',
            'IN_TRANSIT' => 'Your shipment is on its way.',
            'AT_CUSTOMS' => 'Your shipment is going through customs.',
            'OUT_FOR_DELIVERY' => 'Your shipment is out for delivery.',
            'DELIVERED' => 'Your shipment has been delivered.',
            'ON_HOLD' => 'Your shipment is on hold for the moment. We will be back in touch shortly.',
            'RETURNED' => 'Your shipment is on its way back to the sender.',
            'CANCELLED' => 'Your shipment has been cancelled.',
        ],
    ],
    'shipment_brief' => [
        'subject' => '[:tracking] :headline',
        'kind' => [
            'created' => 'New shipment',
            'status' => 'Status updated',
            'updated' => 'Shipment edited',
        ],
        'notified' => 'Notified:',
        'nobody' => 'Nobody was notified: no usable address on this shipment.',
        'skipped' => 'Not sent to:',
        'open' => 'Open the shipment',
    ],
];
