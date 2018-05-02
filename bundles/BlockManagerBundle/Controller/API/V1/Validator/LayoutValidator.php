<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\Bundle\BlockManagerBundle\Controller\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class LayoutValidator extends Validator
{
    /**
     * Validates layout creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    public function validateCreateLayout(Request $request)
    {
        $this->validate(
            $request->request->get('layout_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'layout_type'
        );

        $this->validate(
            $request->request->get('locale'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new LocaleConstraint(),
            ],
            'locale'
        );
    }
}
