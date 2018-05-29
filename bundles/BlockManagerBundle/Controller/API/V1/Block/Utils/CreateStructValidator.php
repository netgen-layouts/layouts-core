<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

final class CreateStructValidator
{
    use ValidatorTrait;

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
