<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class LayoutResolverValidator
{
    use ValidatorTrait;

    /**
     * Validates list of rules from the request when updating priorities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If validation failed
     */
    public function validatePriorities(Request $request)
    {
        $this->validate(
            $request->request->get('rule_ids'),
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'array')),
                new Constraints\All(
                    array(
                        'constraints' => array(
                            new Constraints\NotBlank(),
                            new Constraints\Type(array('type' => 'scalar')),
                        ),
                    )
                ),
            ),
            'rule_ids'
        );
    }
}
