<?php

declare(strict_types=1);

namespace Tests\Providers;

class Constants
{
    public const string STATUS = 'status';
    public const string MESSAGE = 'message';
    public const string DATA = 'data';
    public const string CODE = 'code';
    public const string QR = 'qr';
    public const string SECRET_KEY = 'secretKey';
    public const string BASE64CONTENT = 'base64Content';
    public const string QRCODEINLINE = 'qrCodeInline';
    public const int LENGTH = 16;
    public const string COMPANY_NAME = 'Lion-Packages';
    public const string COMPANY_EMAIL = 'dev@lion.com';
    public const int SIZE = 200;
    public const string ENCODING = 'utf-8';
    public const string SUCCESS = 'success';
    public const int HTTP_CODE_OK = 200;
    public const int HTTP_CODE_UNAUTHORIZED = 401;
    public const string MESSAGE_QR = 'generated QR code';
    public const string AUTHENTICATION_ERROR = 'authentication-error';
    public const string MESSAGE_VERIFY_ERR = 'failed to authenticate, the code is not valid';
}
