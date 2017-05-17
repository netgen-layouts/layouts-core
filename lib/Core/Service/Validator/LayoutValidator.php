<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Symfony\Component\Validator\Constraints;

class LayoutValidator extends Validator
{
    /**
     * Validates layout create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct)
    {
        $layoutName = is_string($layoutCreateStruct->name) ?
            trim($layoutCreateStruct->name) :
            $layoutCreateStruct->name;

        $this->validate(
            $layoutName,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        $layoutDescription = is_string($layoutCreateStruct->description) ?
            trim($layoutCreateStruct->description) :
            $layoutCreateStruct->description;

        if ($layoutDescription !== null) {
            $this->validate(
                $layoutDescription,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'description'
            );
        }

        $this->validate(
            $layoutCreateStruct->layoutType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => LayoutType::class)),
            ),
            'layoutType'
        );

        if ($layoutCreateStruct->shared !== null) {
            $this->validate(
                $layoutCreateStruct->shared,
                array(
                    new Constraints\Type(array('type' => 'bool')),
                ),
                'shared'
            );
        }
    }

    /**
     * Validates layout update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateLayoutUpdateStruct(LayoutUpdateStruct $layoutUpdateStruct)
    {
        $layoutName = is_string($layoutUpdateStruct->name) ?
            trim($layoutUpdateStruct->name) :
            $layoutUpdateStruct->name;

        if ($layoutName !== null) {
            $this->validate(
                $layoutName,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        $layoutDescription = is_string($layoutUpdateStruct->description) ?
            trim($layoutUpdateStruct->description) :
            $layoutUpdateStruct->description;

        if ($layoutDescription !== null) {
            $this->validate(
                $layoutDescription,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'description'
            );
        }
    }

    /**
     * Validates layout create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If the validation failed
     */
    public function validateLayoutCopyStruct(LayoutCopyStruct $layoutCopyStruct)
    {
        $layoutName = is_string($layoutCopyStruct->name) ?
            trim($layoutCopyStruct->name) :
            $layoutCopyStruct->name;

        $this->validate(
            $layoutName,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'name'
        );

        $layoutDescription = is_string($layoutCopyStruct->description) ?
            trim($layoutCopyStruct->description) :
            $layoutCopyStruct->description;

        if ($layoutDescription !== null) {
            $this->validate(
                $layoutDescription,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'description'
            );
        }
    }
}
