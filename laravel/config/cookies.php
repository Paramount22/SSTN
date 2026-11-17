<?php

/*
|--------------------------------------------------------------------------
| Cookie consent
|--------------------------------------------------------------------------
|
| ePrivacy (§ 109 zákona č. 452/2021 Z. z.) and GDPR (čl. 6 ods. 1 písm. a), čl. 7).
| Necessary cookies need no consent. An optional category is offered only while one
| of its services is configured, so the banner appears only when there is something
| to consent to. The category texts live in App\Support\Cookies.
|
*/

return [

    // Raise when categories or services change: every visitor is asked again.
    'version' => 1,

    // First-party cookie that stores the visitor's choice (itself a necessary cookie).
    'cookie' => 'sstn_cookie_consent',

    // How long a choice (accept or reject) is remembered before asking again.
    'lifetime_days' => 180,

    // How long consent records are kept as proof (GDPR čl. 7 ods. 1), then pruned.
    'log_retention_days' => 1095,

    // Shown on the cookie policy page.
    'policy_updated' => '2026-11-17',

    // Google Analytics 4 measurement ID (G-XXXXXXX). Empty = no analytics, no banner.
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),

];
