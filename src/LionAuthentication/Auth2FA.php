<?php

declare(strict_types=1);

namespace Lion\Authentication;

use PragmaRX\Google2FAQRCode\Google2FA;

/**
 * Provides functionality for two-factor authentication (2FA) using Google
 * Authenticator
 *
 * @property Google2FA $google2FA [Google2FA class object]
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
     * Class constructor
     */
	public function __construct()
    {
        $this->google2FA = new Google2FA();
    }

    /**
     * Generates a QR code for two-factor authentication (2FA)
     *
     * @param  string $companyName [The name of the company]
     * @param  string $companyEmail [The company email]
     * @param  int $size [The desired size for the QR code]
     * @param  string $encoding [Text encoding]
     * @param  int $length [The length of the secret key]
     * @param  string $prefix [The optional prefix for the secret key]
     *
     * @return object
     */
	public function qr(
        string $companyName,
        string $companyEmail,
        int $size = 200,
        string $encoding = 'utf-8',
        int $length = 16,
        string $prefix = ''
    ): object {
		$secretKey = $this->google2FA->generateSecretKey($length, $prefix);

		$img = base64_encode(
            $this->google2FA->getQRCodeInline($companyName, $companyEmail, $secretKey, $size, $encoding)
        );

        return (object) [
            'code' => 200,
            'status' => 'success',
            'message' => 'generated QR code',
            'data' => (object) [
                'secretKey' => $secretKey,
                'base64Content' => $img,
                'qr' => "data:image/svg+xml;base64,{$img}"
            ]
        ];
	}

    /**
     * Verifies the authenticity of a given secret code, relative to a given
     * secret key
     *
     * @param  string $secretKey [The secret key used to generate the QR code]
     * @param  string $secretCode [The secret code entered by the user for
     * authentication]
     *
     * @return object
     */
	public function verify(string $secretKey, string $secretCode): object
    {
        $validation = (bool) $this->google2FA->verifyKey($secretKey, $secretCode);

		if (!$validation) {
			return (object) [
                'code' => 401,
				'status' => 'authentication-error',
				'message' => 'failed to authenticate, the code is not valid'
			];
		}

		return (object) [
            'code' => 200,
            'status' => 'success',
            'message' => 'the authentication code is valid',
        ];
	}
}
