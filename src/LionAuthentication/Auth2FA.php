<?php

declare(strict_types=1);

namespace Lion\Authentication;

use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Exceptions\MissingQrCodeServiceException;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FAQRCode\QRCode\Bacon;
use stdClass;

/**
 * Provides functionality for two-factor authentication (2FA) using Google
 * Authenticator
 *
 * @package Lion\Authentication
 */
class Auth2FA
{
    /**
     * [Google2FA class object]
     *
     * @var Google2FA $google2FA
     */
    private Google2FA $google2FA;

    /**
     * [Secret key]
     *
     * @var string $secretKey
     */
    private string $secretKey;

    /**
     * [QR code]
     *
     * @var string $qrCodeInline
     */
    private string $qrCodeInline;

    /**
     * Class constructor
     *
     * @infection-ignore-all
     */
    public function __construct()
    {
        $this->google2FA = new Google2FA();

        $this->google2FA->setQrCodeService(new Bacon());
    }

    /**
     * Generate a secret key
     *
     * @param int $length [The length of the secret key]
     *
     * @return void
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    private function generateSecretKey(int $length): void
    {
        $this->secretKey = $this->google2FA->generateSecretKey($length);
    }

    /**
     * Generate the QR code
     *
     * @param string $companyName [The name of the company]
     * @param string $companyEmail [The company email]
     * @param int $size [The desired size for the QR code]
     * @param string $encoding [Text encoding]
     *
     * @return void
     *
     * @throws MissingQrCodeServiceException
     */
    private function getQRCodeInline(string $companyName, string $companyEmail, int $size, string $encoding): void
    {
        $this->qrCodeInline = $this->google2FA->getQRCodeInline(
            $companyName,
            $companyEmail,
            $this->secretKey,
            $size,
            $encoding
        );
    }

    /**
     * Generates a QR code for two-factor authentication (2FA)
     *
     * @param string $companyName [The name of the company]
     * @param string $companyEmail [The company email]
     * @param int $size [The desired size for the QR code]
     * @param string $encoding [Text encoding]
     * @param int $length [The length of the secret key]
     *
     * @return stdClass
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     * @throws MissingQrCodeServiceException
     *
     * @infection-ignore-all
     */
    public function qr(
        string $companyName,
        string $companyEmail,
        int $size = 200,
        string $encoding = 'utf-8',
        int $length = 16
    ): stdClass {
        $this->generateSecretKey($length);

        $this->getQRCodeInline($companyName, $companyEmail, $size, $encoding);

        $img = base64_encode($this->qrCodeInline);

        return (object) [
            'code' => 200,
            'status' => 'success',
            'message' => 'generated QR code',
            'data' => (object) [
                'secretKey' => $this->secretKey,
                'qrCodeInline' => $this->qrCodeInline,
                'base64Content' => $img,
                'qr' => "data:image/svg+xml;base64,{$img}",
            ],
        ];
    }

    /**
     * Verifies the authenticity of a given secret code, relative to a given
     * secret key
     *
     * @param string $secretKey [The secret key used to generate the QR code]
     * @param string $secretCode [The secret code entered by the user for
     * authentication]
     *
     * @return stdClass
     *
     * @throws InvalidCharactersException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     */
    public function verify(string $secretKey, string $secretCode): stdClass
    {
        $validation = true === $this->google2FA->verifyKey($secretKey, $secretCode);

        if ($validation) {
            return (object) [
                'code' => 200,
                'status' => 'success',
                'message' => 'the authentication code is valid',
            ];
        }

        return (object) [
            'code' => 401,
            'status' => 'authentication-error',
            'message' => 'failed to authenticate, the code is not valid',
        ];
    }
}
