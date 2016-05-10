<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractValue
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int
     */
    protected $version;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param int $version
     * @param int $statusCode
     */
    public function __construct($value, $version, $statusCode = Response::HTTP_OK)
    {
        $this->value = $value;
        $this->version = $version;
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
     * Returns the API version.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
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
