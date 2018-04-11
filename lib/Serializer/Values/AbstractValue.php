<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractValue
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @param mixed $value
     * @param int $statusCode
     */
    public function __construct($value, $statusCode = Response::HTTP_OK)
    {
        $this->value = $value;
        $this->statusCode = $statusCode;
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
