<?php

declare(strict_types=1);

namespace Tests;

use LionAuthentication\Auth2FA;
use LionTest\Test;
use PragmaRX\Google2FA\Google2FA;
use Tests\Constanst;

class Auth2FATest extends Test
{
    private Auth2FA $auth2FA;

    protected function setUp(): void
    {
        $this->auth2FA = new Auth2FA();
    }

    public function testConstruct(): void
    {
        $this->initReflection($this->auth2FA);

        $this->assertInstanceOf(Google2FA::class, $this->getPrivateProperty(Constanst::PROPIERTY));
    }

    public function testQr(): void
    {
        $qr = $this->auth2FA->qr(
            Constanst::COMPANY_NAME,
            Constanst::COMPANY_EMAIL,
            Constanst::SIZE,
            Constanst::ENCODING,
            Constanst::LENGTH,
            Constanst::PREFIX
        );

        $this->assertIsObject($qr);
        $this->assertObjectHasProperty(Constanst::STATUS, $qr);
        $this->assertObjectHasProperty(Constanst::MESSAGE, $qr);
        $this->assertObjectHasProperty(Constanst::DATA, $qr);
        $this->assertSame(Constanst::SUCCESS_QR, $qr->status);
        $this->assertSame(Constanst::MESSAGE_QR, $qr->message);
        $this->assertIsObject($qr->data);
        $this->assertObjectHasProperty(Constanst::SECRET_KEY, $qr->data);
        $this->assertObjectHasProperty(Constanst::QR, $qr->data);
        $this->assertObjectHasProperty(Constanst::BASE64CONTENT, $qr->data);
    }

    public function testVerifyInvalid(): void
    {
        $qr = $this->auth2FA->qr(
            Constanst::COMPANY_NAME,
            Constanst::COMPANY_EMAIL,
            Constanst::SIZE,
            Constanst::ENCODING,
            Constanst::LENGTH,
            Constanst::PREFIX
        );

        $validate = $this->auth2FA->verify($qr->data->secretKey, uniqid());

        $this->assertIsObject($validate);
        $this->assertObjectHasProperty(Constanst::STATUS, $validate);
        $this->assertObjectHasProperty(Constanst::MESSAGE, $validate);
        $this->assertSame(Constanst::SUCCESS_VERIFY_ERR, $validate->status);
        $this->assertSame(Constanst::MESSAGE_VERIFY_ERR, $validate->message);
    }
}
