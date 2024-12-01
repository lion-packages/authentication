<?php

declare(strict_types=1);

namespace Tests;

use Lion\Authentication\Auth2FA;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use PragmaRX\Google2FA\Google2FA;
use ReflectionException;
use Tests\Constants;

class Auth2FATest extends Test
{
    private Auth2FA $auth2FA;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->auth2FA = new Auth2FA();

        $this->initReflection($this->auth2FA);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function construct(): void
    {
        $this->assertInstanceOf(Google2FA::class, $this->getPrivateProperty('google2FA'));
    }

    #[Testing]
    public function qr(): void
    {
        $qr = $this->auth2FA->qr(
            Constants::COMPANY_NAME,
            Constants::COMPANY_EMAIL,
            Constants::SIZE,
            Constants::ENCODING,
            Constants::LENGTH,
            Constants::PREFIX
        );

        $this->assertIsObject($qr);
        $this->assertObjectHasProperty(Constants::STATUS, $qr);
        $this->assertObjectHasProperty(Constants::MESSAGE, $qr);
        $this->assertObjectHasProperty(Constants::DATA, $qr);
        $this->assertSame(Constants::SUCCESS_QR, $qr->status);
        $this->assertSame(Constants::MESSAGE_QR, $qr->message);
        $this->assertIsObject($qr->data);
        $this->assertObjectHasProperty(Constants::SECRET_KEY, $qr->data);
        $this->assertObjectHasProperty(Constants::QR, $qr->data);
        $this->assertObjectHasProperty(Constants::BASE64CONTENT, $qr->data);
    }

    #[Testing]
    public function verifyInvalid(): void
    {
        $qr = $this->auth2FA->qr(
            Constants::COMPANY_NAME,
            Constants::COMPANY_EMAIL,
            Constants::SIZE,
            Constants::ENCODING,
            Constants::LENGTH,
            Constants::PREFIX
        );

        $validate = $this->auth2FA->verify($qr->data->secretKey, uniqid());

        $this->assertIsObject($validate);
        $this->assertObjectHasProperty(Constants::STATUS, $validate);
        $this->assertObjectHasProperty(Constants::MESSAGE, $validate);
        $this->assertSame(Constants::SUCCESS_VERIFY_ERR, $validate->status);
        $this->assertSame(Constants::MESSAGE_VERIFY_ERR, $validate->message);
    }
}
