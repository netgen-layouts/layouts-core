<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

trait ValueStatusTrait
{
    public private(set) Status $status;

    final public bool $isDraft {
        get => $this->status === Status::Draft;
    }

    final public bool $isPublished {
        get => $this->status === Status::Published;
    }

    final public bool $isArchived {
        get => $this->status === Status::Archived;
    }
}
