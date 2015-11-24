<?php

namespace Netgen\BlockManager\API\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;

interface LayoutValidator
{
    /**
     * Validates layout create struct
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If the validation failed
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct);
}
