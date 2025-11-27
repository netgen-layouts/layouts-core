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
        private(set) mixed $value,
        /**
         * Returns the status code of the response that should be used.
         */
        private(set) int $statusCode = Response::HTTP_OK,
    ) {}
}
