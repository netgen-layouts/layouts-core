<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values;

use Netgen\Layouts\API\Values\Status as APIStatus;

enum Status: int
{
    case Draft = 0;
    case Published = 1;
    case Archived = 2;

    public static function fromAPIEnum(APIStatus $apiStatus): self
    {
        return match ($apiStatus) {
            APIStatus::Draft => self::Draft,
            APIStatus::Published => self::Published,
            APIStatus::Archived => self::Archived,
        };
    }
}
