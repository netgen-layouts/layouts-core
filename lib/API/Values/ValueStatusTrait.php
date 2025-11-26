<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

trait ValueStatusTrait
{
    public function isDraft(): bool
    {
        return $this->status === Status::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === Status::Published;
    }

    public function isArchived(): bool
    {
        return $this->status === Status::Archived;
    }
}
