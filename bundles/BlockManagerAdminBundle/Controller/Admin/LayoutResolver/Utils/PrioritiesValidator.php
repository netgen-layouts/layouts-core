<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver\Utils;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class PrioritiesValidator
{
    use ValidatorTrait;

    /**
     * Validates list of rules from the request when updating priorities.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validatePriorities(Request $request): void
    {
        $this->validate(
            $request->request->get('rule_ids'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'array']),
                new Constraints\All(
                    [
                        'constraints' => [
                            new Constraints\NotBlank(),
                            new Constraints\Type(['type' => 'scalar']),
                        ],
                    ]
                ),
            ],
            'rule_ids'
        );
    }
}
