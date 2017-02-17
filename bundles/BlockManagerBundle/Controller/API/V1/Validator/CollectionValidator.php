<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class CollectionValidator extends Validator
{
    /**
     * Validates item creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If validation failed
     */
    public function validateAddItems(Request $request)
    {
        $this->validate(
            $request->request->get('items'),
            array(
                new Constraints\Type(array('type' => 'array')),
                new Constraints\NotBlank(),
                new Constraints\All(
                    array(
                        'constraints' => new Constraints\Collection(
                            array(
                                'fields' => array(
                                    'type' => array(
                                        new Constraints\NotNull(),
                                        new Constraints\Type(array('type' => 'int')),
                                    ),
                                    'value_id' => array(
                                        new Constraints\NotNull(),
                                        new Constraints\Type(array('type' => 'scalar')),
                                    ),
                                    'value_type' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Type(array('type' => 'string')),
                                    ),
                                    'position' => new Constraints\Optional(
                                        array(
                                            new Constraints\NotNull(),
                                            new Constraints\Type(array('type' => 'int')),
                                        )
                                    ),
                                ),
                            )
                        ),
                    )
                ),
            ),
            'items'
        );
    }
}
