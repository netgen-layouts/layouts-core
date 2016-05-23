<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Symfony\Component\Validator\Constraints;

class LayoutValidator extends Validator
{
    /**
     * Validates layout create struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct)
    {
        $this->validate(
            $layoutCreateStruct->name,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        $this->validate(
            $layoutCreateStruct->type,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'type'
        );
    }
}
