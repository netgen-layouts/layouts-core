<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

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
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'layout_type'
        );

        $this->validate(
            $request->request->get('locale'),
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new Constraints\Locale(),
            ),
            'locale'
        );
    }
}
