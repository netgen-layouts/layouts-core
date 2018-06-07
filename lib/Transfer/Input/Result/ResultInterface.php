<?php

namespace Netgen\BlockManager\Transfer\Input\Result;

interface ResultInterface
{
    /**
     * Returns the entity type which was being imported.
     *
     * @return string
     */
    public function getEntityType();

    /**
     * Returns the data which was being imported.
     *
     * @return array
     */
    public function getData();
}
