<?php

namespace Netgen\BlockManager\Transfer\Input;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
interface ImporterInterface
{
    /**
     * Create a new layout from the given $data array.
     *
     * @param array $data
     *
     * @throws \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException If $data is not accepted for import
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function importLayout(array $data);
}
