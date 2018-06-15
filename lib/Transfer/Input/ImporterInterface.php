<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input;

use Traversable;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
interface ImporterInterface
{
    /**
     * Imports the data into the system from provided JSON string.
     *
     * Returns a traversable instance holding the results of the import as
     * \Netgen\BlockManager\Transfer\Input\Result\ResultInterface objects
     */
    public function importData(string $data): Traversable;
}
