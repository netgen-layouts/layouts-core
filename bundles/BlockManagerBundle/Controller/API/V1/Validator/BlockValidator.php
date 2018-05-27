<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\Bundle\BlockManagerBundle\Controller\Validator\Validator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

final class BlockValidator extends Validator
{
    /**
     * Validates block create parameters from the provided parameter bag.
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $data
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validateCreateBlock(ParameterBag $data)
    {
        $this->validate(
            $data->get('block_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'block_type'
        );
    }
}
