<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderStatusEnum extends Enum
{
    #[Description('Pending')]
    const PENDING = 1;

    #[Description('Menunggu Konfirmasi')]
    const WAITING_CONFIRMATION = 2;

    #[Description('Sukses')]
    const SUCCESS = 3;

    #[Description('Gagal')]
    const FAILED = 4;

    #[Description('Expired')]
    const EXPIRED = 5;

    #[Description('Challenge')]
    const CHALLENGE = 6;

    #[Description('Dibatalkan')]
    const CANCEL = 7;

    #[Description('Ditolak')]
    const REJECT = 8;

    public static function forFilterOrder(): array
    {
        return [
            self::PENDING,
            self::WAITING_CONFIRMATION,
            self::SUCCESS,
            self::FAILED,
            self::EXPIRED,
            self::CHALLENGE,
            self::CANCEL,
            self::REJECT,
        ];
    }
}
