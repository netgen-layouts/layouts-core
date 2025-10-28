<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractValue
{
    private mixed $value;

    private int $statusCode;

    public function __construct(mixed $value, int $statusCode = Response::HTTP_OK)
    {
        $this->value = $value;
        $this->statusCode = $statusCode;
    }

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
