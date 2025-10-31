<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter as BaseParamConverter;
use Netgen\Layouts\API\Values\Status;
use Ramsey\Uuid\Uuid;

final class ParamConverter extends BaseParamConverter
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
