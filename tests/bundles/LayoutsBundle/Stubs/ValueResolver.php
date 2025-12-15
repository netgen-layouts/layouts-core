<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Status as ValueResolverStatus;
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

    public function loadValue(array $parameters): Value
    {
        $status = match ($parameters['status']) {
            ValueResolverStatus::Published => Status::Published,
            ValueResolverStatus::Archived => Status::Archived,
            default => Status::Draft,
        };

        unset($parameters['status']);

        return Value::fromArray([...$parameters, ...['id' => Uuid::fromString($parameters['id']), 'status' => $status]]);
    }
}
