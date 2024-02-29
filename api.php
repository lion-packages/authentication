<?php

require_once('./vendor/autoload.php');

header('Content-Type: application/json');

use Lion\Authentication\Auth2FA;

$content = json_decode(file_get_contents("php://input"), true);
$request = $content === null ? ((object) [...$_POST, ...$_GET]) : ((object) $content);

die(json_encode(
    (new Auth2FA())
        ->verify('IADHJIVC2MO3EVM2', $request->code)
));
