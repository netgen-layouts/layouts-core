<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class BlockCollectionValidator extends Validator
{
    use ValidatorTrait;

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
            $request->request->get('new_type'),
            array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            BlockCollectionController::NEW_TYPE_MANUAL,
                            BlockCollectionController::NEW_TYPE_DYNAMIC,
                            BlockCollectionController::NEW_TYPE_SHARED,
                        ),
                        'strict' => true,
                    )
                ),
            ),
            'new_type'
        );
    }
}
