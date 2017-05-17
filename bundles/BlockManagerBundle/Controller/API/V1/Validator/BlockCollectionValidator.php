<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class BlockCollectionValidator extends Validator
{
    /**
     * Validates block creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If validation failed
     */
    public function validateChangeCollectionType(Request $request)
    {
        $this->validate(
            (int) $request->request->get('new_type'),
            array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            Collection::TYPE_MANUAL,
                            Collection::TYPE_DYNAMIC,
                        ),
                        'strict' => true,
                    )
                ),
            ),
            'new_type'
        );
    }
}
