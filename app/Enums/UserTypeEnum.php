<?php

namespace App\Enums;

class UserTypeEnum
{
    // User Type
    const DISBANDED = 0;

    const COORD = 1;

    const BOARD = 2;

    const PENDING = 3;

    const OUTGOING = 4;

    const INCOMING = 5;

   public static function label(?int $typeId): string
    {
        if ($typeId === null) return 'N/A';

        return match($typeId) {
            self::DISBANDED => 'Disbanded',
            self::COORD     => 'Coordinator',
            self::BOARD     => 'Board',
            self::PENDING   => 'Pending',
            self::OUTGOING  => 'Outgoing',
            self::INCOMING  => 'Incoming',
            default         => 'N/A',
        };
    }
}
