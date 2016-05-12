<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class BlockValidator
{
    use ValidatorTrait;

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
    }
}
