<?php

require_once('./vendor/autoload.php');

use Lion\Authentication\Auth2FA;

header('Content-Type: application/json');

// IADHJIVC2MO3EVM2
die(json_encode(
    (new Auth2FA())
        ->qr('Lion-Packages', 'sergioleon4004@gmail.com', 200)
));
