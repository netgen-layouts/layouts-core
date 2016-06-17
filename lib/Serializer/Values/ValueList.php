<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

class ValueList extends AbstractValue implements ValueInterface
{
    /**
     * Constructor.
     *
     * @param array $values
     * @param int $statusCode
     */
    public function __construct(array $values, $statusCode = Response::HTTP_OK)
    {
        parent::__construct($values, $statusCode);
    }
}
