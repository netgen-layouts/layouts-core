<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

interface ResultInterface
{
    /**
     * Returns the entity type which was being imported.
     */
    public function getEntityType(): string;

    /**
     * Returns the data which was being imported.
     */
    public function getData(): array;
}
