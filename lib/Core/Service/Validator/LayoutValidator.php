<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
use Symfony\Component\Validator\Constraints;

class LayoutValidator extends Validator
{
    /**
     * Validates layout create struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct)
    {
        $this->validate(
            trim($layoutCreateStruct->name),
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

        return true;
    }

    /**
     * Validates layout update struct.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateLayoutUpdateStruct(LayoutUpdateStruct $layoutUpdateStruct)
    {
        $this->validate(
            trim($layoutUpdateStruct->name),
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        return true;
    }
}
