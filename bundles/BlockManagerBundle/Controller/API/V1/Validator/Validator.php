<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

abstract class Validator
{
    use ValidatorTrait;

    /**
     * Validates offset and limit parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If validation failed
     */
    public function validateOffsetAndLimit(Request $request)
    {
        $this->validate(
            $request->query->get('offset'),
            array(
                new Constraints\Type(array('type', 'numeric')),
                new Constraints\GreaterThanOrEqual(array('value' => 0)),
                new Constraints\NotBlank(),
            )
        );

        $limit = $request->query->get('limit');

        if ($limit !== null) {
            $this->validate(
                $limit,
                array(
                    new Constraints\Type(array('type', 'numeric')),
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                    new Constraints\LessThanOrEqual(array('value' => 25)),
                    new Constraints\NotBlank(),
                )
            );
        }
    }
}
