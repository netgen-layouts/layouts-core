<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

interface CollectionRunnerInterface
{
    public function __invoke(Collection $collection, $offset, $limit);

    public function count(Collection $collection);
}
