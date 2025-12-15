<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public static function fromPersistenceEnum(PersistenceStatus $apiStatus): self
    {
        return match ($apiStatus) {
            PersistenceStatus::Draft => self::Draft,
            PersistenceStatus::Published => self::Published,
            PersistenceStatus::Archived => self::Archived,
        };
    }
}
