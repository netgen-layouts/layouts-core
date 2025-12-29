<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver as BaseValueResolver;
use Symfony\Component\Uid\Uuid;

final class ValueResolver extends BaseValueResolver
{
    protected function getSourceAttributeNames(): array
    {
        return ['id'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'value';
    }

    protected function getSupportedClass(): string
    {
        return Value::class;
    }

    protected function loadValue(array $parameters): Value
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
