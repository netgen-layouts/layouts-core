<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class LayoutValidator
{
    use ValidatorTrait;

    /**
     * Validates layout creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If validation failed
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
