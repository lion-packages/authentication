<?php

declare(strict_types=1);

namespace LionAuthentication;

use PragmaRX\Google2FAQRCode\Google2FA;

class Auth2FA
{
	private Google2FA $google2FA;

	public function __construct()
    {
        $this->google2FA = new Google2FA();
    }

	public function qr(string $companyName, string $companyEmail, int $size = 200, string $encoding = 'utf-8'): object
    {
		$secretKey = $this->google2FA->generateSecretKey();

		$img = base64_encode(
            $this->google2FA->getQRCodeInline($companyName, $companyEmail, $secretKey, $size, $encoding)
        );

        return (object) [
            'status' => 'success',
            'message' => 'generated QR code',
            'data' => (object) [
                'secretKey' => $secretKey,
                'qr' => "data:image/svg+xml;base64,{$img}"
            ]
        ];
	}

	public function verify(string $secretKey, string $secretCode): object
    {
        $validation = (bool) $this->google2FA->verifyKey($secretKey, $secretCode);

		if (!$validation) {
			return (object) [
				'status' => 'authentication-error',
				'message' => "failed to authenticate, the code '{$secretCode}' is not valid"
			];
		}

		return (object) ['status' => 'success', 'message' => 'the authentication code is valid'];
	}
}
