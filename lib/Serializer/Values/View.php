<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

/**
 * Represents a serialized value with a view, in a specified API version.
 */
final class View extends AbstractValue
{
    /**
     * @var int
     */
    private $version;

    /**
     * @param mixed $value
     * @param int $version
     * @param int $statusCode
     */
    public function __construct($value, int $version, int $statusCode = Response::HTTP_OK)
    {
        parent::__construct($value, $statusCode);

        $this->version = $version;
    }

    /**
     * Returns the API version.
     */
    public function getVersion(): int
    {
        return $this->version;
    }
}
