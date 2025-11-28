<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Values;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractValue
{
    public function __construct(
        /**
         * Returns the serialized value.
         */
        public private(set) mixed $value,
        /**
         * Returns the status code of the response that should be used.
         */
        public private(set) int $statusCode = Response::HTTP_OK,
    ) {}
}
