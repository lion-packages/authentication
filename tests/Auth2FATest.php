<?php

declare(strict_types=1);

namespace Tests;

use Lion\Authentication\Auth2FA;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test as Testing;
use PHPUnit\Framework\Attributes\TestWith;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Exceptions\MissingQrCodeServiceException;
use ReflectionException;
use stdClass;
use Tests\Providers\Auth2FAProviderTrait;

class Auth2FATest extends Test
{
    use Auth2FAProviderTrait;

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
    protected function tearDown(): void
    {
        $this->setPrivateProperty('secretKey', '');

        $this->setPrivateProperty('qrCodeInline', '');
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function construct(): void
    {
        $this->assertInstanceOf(Google2FA::class, $this->getPrivateProperty('google2FA'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    #[TestWith(['length' => 16])]
    #[TestWith(['length' => 32])]
    public function generateSecretKey(int $length): void
    {
        $this->getPrivateMethod('generateSecretKey', [
            'length' => $length,
        ]);

        /** @var string $secretKey */
        $secretKey = $this->getPrivateProperty('secretKey');

        $this->assertSame($length, strlen($secretKey));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    #[DataProvider('getQRCodeInlineProvider')]
    public function getQRCodeInline(string $companyName, string $companyEmail, int $size, string $encoding): void
    {
        $this->getPrivateMethod('generateSecretKey', [
            'length' => Constants::LENGTH,
        ]);

        $this->getPrivateMethod('getQRCodeInline', [
            'companyName' => $companyName,
            'companyEmail' => $companyEmail,
            'size' => $size,
            'encoding' => $encoding,
        ]);

        /** @var string $qrCodeInline */
        $qrCodeInline = $this->getPrivateProperty('qrCodeInline');

        $this->assertTrue(
            str_starts_with($qrCodeInline, 'data:image/png;base64,')
            || str_starts_with($qrCodeInline, '<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws MissingQrCodeServiceException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    #[Testing]
    #[DataProvider('qrProvider')]
    public function qr(string $companyName, string $companyEmail, int $size, string $encoding, int $length): void
    {
        $qr = $this->auth2FA->qr($companyName, $companyEmail, $size, $encoding, $length);

        $this->assertInstanceOf(stdClass::class, $qr);
        $this->assertObjectHasProperty(Constants::STATUS, $qr);
        $this->assertObjectHasProperty(Constants::MESSAGE, $qr);
        $this->assertObjectHasProperty(Constants::DATA, $qr);
        $this->assertObjectHasProperty(Constants::CODE, $qr);
        $this->assertIsString($qr->status);
        $this->assertIsInt($qr->code);
        $this->assertIsString($qr->message);
        $this->assertIsObject($qr->data);
        $this->assertSame(Constants::SUCCESS, $qr->status);
        $this->assertSame(Constants::MESSAGE_QR, $qr->message);
        $this->assertSame(Constants::HTTP_CODE_OK, $qr->code);
        $this->assertIsObject($qr->data);
        $this->assertInstanceOf(stdClass::class, $qr->data);
        $this->assertObjectHasProperty(Constants::SECRET_KEY, $qr->data);
        $this->assertObjectHasProperty(Constants::QR, $qr->data);
        $this->assertObjectHasProperty(Constants::BASE64CONTENT, $qr->data);
        $this->assertObjectHasProperty(Constants::QRCODEINLINE, $qr->data);
        $this->assertIsString($qr->data->secretKey);
        $this->assertIsString($qr->data->qr);
        $this->assertIsString($qr->data->base64Content);
        $this->assertIsString($qr->data->qrCodeInline);
        $this->assertSame($length, strlen($qr->data->secretKey));

        $this->assertTrue(
            str_starts_with($qr->data->qrCodeInline, 'data:image/png;base64,')
            || str_starts_with($qr->data->qrCodeInline, '<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws MissingQrCodeServiceException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    #[Testing]
    #[DataProvider('qrProvider')]
    public function verifyInvalid(
        string $companyName,
        string $companyEmail,
        int $size,
        string $encoding,
        int $length
    ): void {
        $qr = $this->auth2FA->qr($companyName, $companyEmail, $size, $encoding, $length);

        $this->assertInstanceOf(stdClass::class, $qr);
        $this->assertObjectHasProperty(Constants::STATUS, $qr);
        $this->assertObjectHasProperty(Constants::MESSAGE, $qr);
        $this->assertObjectHasProperty(Constants::DATA, $qr);
        $this->assertObjectHasProperty(Constants::CODE, $qr);
        $this->assertIsString($qr->status);
        $this->assertIsInt($qr->code);
        $this->assertIsString($qr->message);
        $this->assertIsObject($qr->data);
        $this->assertInstanceOf(stdClass::class, $qr->data);
        $this->assertSame(Constants::SUCCESS, $qr->status);
        $this->assertSame(Constants::MESSAGE_QR, $qr->message);
        $this->assertSame(Constants::HTTP_CODE_OK, $qr->code);
        $this->assertObjectHasProperty(Constants::SECRET_KEY, $qr->data);
        $this->assertObjectHasProperty(Constants::QR, $qr->data);
        $this->assertObjectHasProperty(Constants::QRCODEINLINE, $qr->data);
        $this->assertObjectHasProperty(Constants::BASE64CONTENT, $qr->data);
        $this->assertIsString($qr->data->qr);
        $this->assertIsString($qr->data->secretKey);
        $this->assertIsString($qr->data->qrCodeInline);
        $this->assertIsString($qr->data->base64Content);

        $validate = $this->auth2FA->verify($qr->data->secretKey, uniqid());

        $this->assertInstanceOf(stdClass::class, $validate);
        $this->assertObjectHasProperty(Constants::STATUS, $qr);
        $this->assertObjectHasProperty(Constants::CODE, $qr);
        $this->assertObjectHasProperty(Constants::MESSAGE, $qr);
        $this->assertIsString($qr->status);
        $this->assertIsInt($qr->code);
        $this->assertIsString($qr->message);
        $this->assertSame(Constants::AUTHENTICATION_ERROR, $validate->status);
        $this->assertSame(Constants::HTTP_CODE_UNAUTHORIZED, $validate->code);
        $this->assertSame(Constants::MESSAGE_VERIFY_ERR, $validate->message);
    }
}
