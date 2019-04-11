<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Utils;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

final class CreateStructValidator
{
    use ValidatorTrait;

    /**
     * Validates block create parameters from the provided parameter bag.
     */
    public function validateCreateBlock(ParameterBag $data): void
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
