<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class LayoutValidator extends Validator
{
    /**
     * Validates layout creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationFailedException If validation failed
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
    }
}
