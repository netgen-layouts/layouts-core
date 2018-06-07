<?php

namespace Netgen\BlockManager\Transfer\Input;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
interface ImporterInterface
{
    /**
     * Imports the data into the system from provided JSON string.
     *
     * @param string $data
     *
     * @return \Traversable A traversable instance holding the results of the import as
     *                      \Netgen\BlockManager\Transfer\Input\Result\ResultInterface objects
     */
    public function importData($data);
}
