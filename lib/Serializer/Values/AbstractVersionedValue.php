<?php

namespace Netgen\BlockManager\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractVersionedValue extends AbstractValue
{
    /**
     * @var int
     */
    private $version;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param int $version
     * @param int $statusCode
     */
    public function __construct($value, $version, $statusCode = Response::HTTP_OK)
    {
        parent::__construct($value, $statusCode);

        $this->version = $version;
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
}
