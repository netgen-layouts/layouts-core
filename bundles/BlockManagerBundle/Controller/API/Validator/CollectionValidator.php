<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class CollectionValidator
{
    use ValidatorTrait;

    /**
     * Validates block moving parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If validation failed
     */
    public function validateMove(Request $request)
    {
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
