<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;

abstract class Validator
{
    use ValidatorTrait;

    /**
     * Validates offset and limit parameters.
     *
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\ValidationFailedException If validation failed
     */
    public function validateOffsetAndLimit($offset, $limit)
    {
        if (!empty($offset)) {
            $this->validate(
                $offset,
                array(
                    new Constraints\Type(array('type' => 'numeric')),
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                    new Constraints\NotBlank(),
                )
            );
        }

        if (!empty($limit)) {
            $this->validate(
                $limit,
                array(
                    new Constraints\Type(array('type' => 'numeric')),
                    new Constraints\GreaterThan(array('value' => 0)),
                    new Constraints\NotBlank(),
                )
            );
        }
    }
}
