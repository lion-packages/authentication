<?php

namespace LionAuthentication;

use PragmaRX\Google2FAQRCode\Google2FA;

class Auth2FA {

	private static Google2FA $google2FA;
	private static ?Auth2FA $auth = null;
	private static string $secret_key;

	public static function init(): Auth2FA {
		if (self::$auth === null) {
			self::$auth = new Auth2FA();
		}

		self::$google2FA = new Google2FA();
		return self::$auth;
	}

	public static function qr(string $companyName, string $companyEmail, int $size = 400): object {
		self::$secret_key = self::$google2FA->generateSecretKey();

		$img = base64_encode(self::$google2FA->getQRCodeInline(
			$companyName,
			$companyEmail,
			self::$secret_key,
			$size
		));

		return (object) [
			'qr' => "data:image/svg+xml;base64," . $img,
			'secret-key' => self::$secret_key
		];
	}

	public static function verify(string $secret_key, string $secret_code): object {
		if (!self::$google2FA->verifyKey($secret_key, $secret_code)) {
			return (object) [
				'status' => 'authentication-error',
				'message' => "failed to authenticate, the code is not valid"
			];
		}

		return (object) [
			'status' => 'success',
			'message' => "the authentication code is valid"
		];
	}

}