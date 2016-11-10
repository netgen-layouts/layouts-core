<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * Validates block create parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If validation failed
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
