<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Traversable;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
interface ImporterInterface
{
    /**
     * Imports the data into the system from provided JSON string.
     *
     * @return \Traversable<\Netgen\Layouts\Transfer\Input\Result\ResultInterface>
     */
    public function importData(string $data): Traversable;
}
