<?php

return [
    'sections' => [
        1 => "Data controller",
        2 => "Data we collect",
        3 => "Why we use it",
        4 => "Who receives your data",
        5 => "How long we keep it",
        6 => "Cookies",
        7 => "Your rights",
        8 => "Security",
        9 => "Transfers outside the European Union",
        10 => "Contact and complaints",
        11 => "Changes to this policy",
    ],
    'intro' => "This policy describes the data :name: collects when you browse this site, contact us, or track a shipment, why, for how long, and how to exercise your rights over it. It covers the public site only — not the back office reserved for authorised staff.",
    'table' => [
        'headers' => ["Data", "Purpose", "Legal basis"],
        'rows' => [
            ["Contact form", "Respond to your enquiry", "Legitimate interest in handling enquiries addressed to us"],
            ["Shipment data", "Arrange, carry out and invoice the transport", "Performance of the transport contract"],
            ["Invoices and accounting records", "Bookkeeping", "Legal obligation"],
            ["IP address (contact form)", "Prevent abuse (spam, automated submissions)", "Legitimate interest in securing the service"],
        ],
    ],
    'articles' => [
        1 => "<p>The controller for the data described below is :name:, whose full details appear in the :notice_link:. For any question about your data, write to :email_link:.</p>",
        2 => "<p>We collect only the data needed for the functions below.</p>
              <ul>
                <li><strong>Contact form.</strong> Name, e-mail, phone (optional), subject and message, together with the IP address and browser used when the form is sent — useful for identifying abuse of the form.</li>
                <li><strong>Shipments.</strong> When a shipment is booked on your behalf, we keep the name, e-mail, phone number and address (street, city, country) of the shipper and the receiver, together with the contents and route of the shipment.</li>
                <li><strong>Shipment tracking.</strong> The tracking page shows the full shipment record to anyone who enters its tracking number — that number acts as the access credential. Only share it with people involved in the shipment.</li>
                <li><strong>Technical data.</strong> A session cookie strictly necessary for the site to function (see §6). We use no advertising cookie and no audience-measurement tool.</li>
              </ul>",
        3 => "<p>We process each category of data for a specific reason, never for a purpose it doesn't justify:</p>",
        4 => "<p>Your data is accessible to authorised :name: staff, and is shared with:</p>
              <ul>
                <li><strong>Resend</strong> (e-mail delivery provider) — to send you the confirmation and notification e-mails we owe you.</li>
                <li><strong>OpenStreetMap</strong> — whenever a map is shown (contact page, shipment tracking), your browser loads the map imagery directly from OpenStreetMap's servers, which discloses your IP address to them. No other data is shared with this service.</li>
                <li><strong>Geoapify / LocationIQ</strong> — address-lookup tools our team uses internally when booking a shipment, to turn an address into a map position.</li>
              </ul>
              <p>We do not sell or rent your data to third parties, and do not use it for advertising purposes.</p>",
        5 => "<ul>
                <li><strong>Contact form messages:</strong> up to 24 months from the last exchange, then deleted.</li>
                <li><strong>Shipment and invoicing records:</strong> kept for the statutory accounting-record retention period, 10 years in France.</li>
                <li><strong>Session cookie:</strong> cleared when you close your browser, or after :hours: hour(s) of inactivity.</li>
              </ul>",
        6 => "<p>This site sets a single cookie, strictly necessary for it to work: a session cookie that keeps you signed in from page to page and protects forms against forged submissions (a CSRF token). It is used for neither advertising tracking nor audience measurement, and therefore requires no consent under applicable e-privacy law.</p>
              <p>We use no audience-measurement tool (Google Analytics or similar), advertising cookie, or social-network cookie. Should that change, a compliant consent banner would be put in place before any non-essential cookie is set.</p>",
        7 => "<p>Under the General Data Protection Regulation (GDPR), you have the following rights over your data:</p>
              <ul>
                <li><strong>Access:</strong> obtain a copy of the data we hold about you.</li>
                <li><strong>Rectification:</strong> have inaccurate or incomplete data corrected.</li>
                <li><strong>Erasure:</strong> request deletion of your data, within the limits of our legal retention obligations.</li>
                <li><strong>Restriction:</strong> request that a processing activity be temporarily suspended.</li>
                <li><strong>Portability:</strong> receive your data in a structured, reusable format.</li>
                <li><strong>Objection:</strong> object to processing based on our legitimate interest.</li>
              </ul>
              <p>To exercise any of these rights, write to :email_link:. We reply within one month. If you believe your rights are not being respected, you may lodge a complaint with the :cnil_link: (the French data protection authority).</p>",
        8 => "<p>Exchanges with this site are encrypted (HTTPS). Access to the back office is restricted to authorised staff, password-protected, and each account can be individually deactivated. No system is foolproof, so we limit collection to the data described above.</p>",
        9 => "<p>We aim to keep your data within the European Union. OpenStreetMap, used to display maps, is operated by a foundation based in the United Kingdom, a country the European Commission recognises as offering an adequate level of data protection.</p>",
        10 => "<p>For any question about this policy or your data, write to :email_link:. Given the size of the company and the nature of the processing described above, appointing a data protection officer (DPO) is not required by the regulation.</p>",
        11 => "<p>This policy may be updated, in particular to reflect changes to the site or to the regulation. The date of the last update appears at the top of this page; we encourage you to check it periodically.</p>",
    ],
    'link_notice' => "legal notice",
];
