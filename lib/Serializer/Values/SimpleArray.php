<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

class SimpleArray implements ValueInterface
{
    /**
     * @var array
     */
    protected $value = array();

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * Constructor.
     *
     * @param array $value
     * @param int $statusCode
     */
    public function __construct(array $value, $statusCode = Response::HTTP_OK)
    {
        $this->value = $value;
        $this->statusCode = $statusCode;
    }

    /**
     * Returns the value.
     *
     * @return array
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
