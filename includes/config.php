<?php
declare(strict_types=1);

/**
 * Site configuration.
 * Set real credentials through environment variables (recommended) or edit the fallbacks below.
 * Never commit real passwords to version control.
 */
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'user' => getenv('DB_USER') ?: 'mtelgon_user',
        'pass' => getenv('DB_PASS') ?: '',
        'name' => getenv('DB_NAME') ?: 'mtelgon_iclip',
    ],
    'site' => [
        'name'     => 'Mt. Elgon-ICLIP',
        'url'      => getenv('SITE_URL') ?: 'https://mtelgon-iclip.org',
        'email'    => 'info@mtelgon-iclip.org',
        'phone'    => '+254768526791',
        'phone_ui' => '+254 768 526 791',
    ],
    // Best-effort email notification when an inquiry arrives (needs a working mail() on the server).
    'notify_inquiries' => true,
    // Secret used to hash visitor IPs before storing them (rate limiting only). Change it.
    'ip_salt' => getenv('IP_SALT') ?: 'change-this-long-random-string',
];
