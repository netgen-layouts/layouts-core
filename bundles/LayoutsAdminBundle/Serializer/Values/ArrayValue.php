<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

/**
 * Represents a serialized array.
 */
final class ArrayValue extends AbstractValue
{
    /**
     * @param mixed[] $value
     */
    public function __construct(array $value, int $statusCode = Response::HTTP_OK)
    {
        parent::__construct($value, $statusCode);
    }
}
