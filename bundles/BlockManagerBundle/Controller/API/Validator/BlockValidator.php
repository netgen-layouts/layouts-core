<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\Validator;

use Netgen\Bundle\BlockManagerBundle\Controller\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * Validates block creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If validation failed
     */
    public function validateCreateBlock(Request $request)
    {
        $this->validate(
            $request->request->get('block_type'),
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'block_type'
        );

        $this->validate(
            $request->request->get('layout_id'),
            array(
                new Constraints\GreaterThan(array('value' => 0)),
                new Constraints\Type(array('type' => 'int')),
            ),
            'layout_id'
        );

        $this->validate(
            $request->request->get('zone_identifier'),
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'zone_identifier'
        );

        $position = $request->request->get('position');
        if ($position !== null) {
            $this->validate(
                $position,
                array(
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                    new Constraints\Type(array('type' => 'int')),
                ),
                'position'
            );
        }
    }

    /**
     * Validates block moving parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If validation failed
     */
    public function validateMoveBlock(Request $request)
    {
        $zoneIdentifier = $request->request->get('zone_identifier');
        if ($zoneIdentifier !== null) {
            $this->validate(
                $zoneIdentifier,
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                ),
                'zone_identifier'
            );
        }

        $this->validate(
            $request->request->get('position'),
            array(
                new Constraints\GreaterThanOrEqual(array('value' => 0)),
                new Constraints\Type(array('type' => 'int')),
            ),
            'position'
        );
    }
}
