<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver as BaseValueResolver;
use Netgen\Layouts\API\Values\Status;
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

    public function loadValue(array $values): Value
    {
        $status = match ($values['status']) {
            'published' => Status::Published,
            'archived' => Status::Archived,
            default => Status::Draft,
        };

        unset($values['status']);

        return Value::fromArray([...$values, ...['id' => Uuid::fromString($values['id']), 'status' => $status]]);
    }
}
