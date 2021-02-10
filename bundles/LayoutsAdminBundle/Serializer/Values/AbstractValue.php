<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractValue
{
    /**
     * @var mixed
     */
    private $value;

    private int $statusCode;

    /**
     * @param mixed $value
     */
    public function __construct($value, int $statusCode = Response::HTTP_OK)
    {
        $this->value = $value;
        $this->statusCode = $statusCode;
    }

    /**
     * Returns the serialized value.
     *
     * @return mixed
     */
    public function getValue()
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
