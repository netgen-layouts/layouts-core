<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\Request;

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
        $items = $request->request->get('items');
        if (!is_array($items) || empty($items)) {
            throw new ValidationFailedException('Item list is invalid.');
        }

        foreach ($items as $index => $item) {
            foreach (array('type', 'value_id', 'value_type') as $param) {
                if (!array_key_exists($param, $item)) {
                    throw new ValidationFailedException(
                        sprintf(
                            'The "%s" property is missing in item no. %d.',
                            $param,
                            $index
                        )
                    );
                }
            }
        }
    }
}
