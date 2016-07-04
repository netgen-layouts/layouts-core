<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;

class CollectionValidator
{
    use ValidatorTrait;

    /**
     * Validates item creation parameters from the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If validation failed
     */
    public function validateAddItems(Request $request)
    {
        $items = $request->request->get('items');
        if (!is_array($items) || empty($items)) {
            throw new InvalidArgumentException('items', 'Item list is invalid.');
        }

        foreach ($items as $item) {
            foreach (array('type', 'value_id', 'value_type') as $param) {
                if (!isset($item[$param])) {
                    throw new InvalidArgumentException($param, 'The value is missing.');
                }
            }
        }
    }
}
