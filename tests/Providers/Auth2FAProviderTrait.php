<?php

declare(strict_types=1);

namespace Tests\Providers;

use Tests\Constants;

trait Auth2FAProviderTrait
{
    /**
     * @return array<int, array{
     *     companyName: string,
     *     companyEmail: string,
     *     size: int,
     *     encoding: string
     * }>
     */
    public static function getQRCodeInlineProvider(): array
    {
        return [
            [
                'companyName' => 'Lion-Packages',
                'companyEmail' => 'dev@lion.com',
                'size' => 200,
                'encoding' => 'utf-8',
            ],
            [
                'companyName' => 'Lion-Packages-Open-Source',
                'companyEmail' => 'dev@lion.com',
                'size' => 250,
                'encoding' => 'utf-8',
            ],
        ];
    }

    /**
     * @return array<int, array{
     *     companyName: string,
     *     companyEmail: string,
     *     size: int,
     *     encoding: string,
     *     length: int
     * }>
     */
    public static function qrProvider(): array
    {
        return [
            [
                'companyName' => Constants::COMPANY_NAME,
                'companyEmail' => Constants::COMPANY_EMAIL,
                'size' => Constants::SIZE,
                'encoding' => Constants::ENCODING,
                'length' => Constants::LENGTH,
            ],
            [
                'companyName' => 'Lion-Packages-Open-Source',
                'companyEmail' => 'lion@lion.com',
                'size' => Constants::SIZE + 50,
                'encoding' => Constants::ENCODING,
                'length' => 32,
            ],
        ];
    }
}
