<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractValue
{
    public function __construct(
        private mixed $value,
        private int $statusCode = Response::HTTP_OK,
    ) {}

    /**
     * Returns the serialized value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Returns the status code of the response that should be used.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
