<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IP and user-agent retention
    |--------------------------------------------------------------------------
    |
    | ip_address and user_agent exist on a contact message only to spot abuse near
    | the time it happened; the comment on the column says as much. Kept forever,
    | they turn every visitor's submission into an indefinite record of who
    | contacted the company and from where. contact-messages:prune clears both,
    | this many days after the message arrived — the message itself, and the
    | name and email a reply needs, are left alone.
    |
    */

    'triage_retention_days' => (int) env('CONTACT_TRIAGE_RETENTION_DAYS', 30),

];
