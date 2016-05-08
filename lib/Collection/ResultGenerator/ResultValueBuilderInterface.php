<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

interface ResultValueBuilderInterface
{
    /**
     * Builds the result value from provided object.
     *
     * @param mixed $object
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    public function build($object);
}
