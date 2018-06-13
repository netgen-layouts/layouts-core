<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils;

use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

final class CreateStructValidator
{
    use ValidatorTrait;

    /**
     * Validates layout creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $data
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validateCreateLayout(ParameterBag $data)
    {
        $this->validate(
            $data->get('layout_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'layout_type'
        );

        $this->validate(
            $data->get('locale'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new LocaleConstraint(),
            ],
            'locale'
        );
    }
}
