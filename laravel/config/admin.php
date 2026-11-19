<?php

return [

    /*
    | URL path of the administration and of the login, password and profile pages,
    | e.g. "sprava-7k2q9x" gives https://sstn.sk/sprava-7k2q9x/login. Use a hard-to-guess
    | value in production: /admin, /login and /forgot-password then return 404, which keeps
    | automated scanners away. It is not a password; the admin stays protected by login.
    */
    'path' => trim((string) env('ADMIN_PATH', 'admin'), '/') ?: 'admin',

];
