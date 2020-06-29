<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Netgen\Layouts\API\Values\Value;

interface EntityImporterInterface
{
    /**
     * Imports an entity from the given serialized $data.
     *
     * @param array<string, mixed> $data
     */
    public function importEntity(array $data): Value;
}
