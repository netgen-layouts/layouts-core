<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\Bundle\BlockManagerBundle\Controller\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class BlockValidator extends Validator
{
    /**
     * Validates block create parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validateCreateBlock(Request $request)
    {
        $this->validate(
            $request->request->get('block_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'block_type'
        );
    }
}
