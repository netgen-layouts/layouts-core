<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver as BaseValueResolver;
use Symfony\Component\Uid\Uuid;

final class ValueResolver extends BaseValueResolver
{
    public function getSourceAttributeNames(): array
    {
        return ['id'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'value';
    }

    public function getSupportedClass(): string
    {
        return Value::class;
    }

    public function loadValue(array $parameters): Value
    {
        return Value::fromArray(
            [
                ...$parameters,
                ...[
                    'id' => Uuid::fromString($parameters['id']),
                    'status' => $parameters['status'],
                ],
            ],
        );
    }
}
