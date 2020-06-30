<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Netgen\Layouts\API\Values\Value;

interface EntityImporterInterface
{
    /**
     * Imports an entity from the given serialized $data.
     *
     * If $overwriteExisting flag is true, the entities with the same UUID as the
     * ones in provided data need to be deleted.
     *
     * @param array<string, mixed> $data
     */
    public function importEntity(array $data, bool $overwriteExisting): Value;
}
