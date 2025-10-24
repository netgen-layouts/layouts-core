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
        $status = Status::Draft;
        if ($values['status'] === 'published') {
            $status = Status::Published;
        } elseif ($values['status'] === 'archived') {
            $status = Status::Archived;
        }

        unset($values['status']);

        return Value::fromArray(['id' => Uuid::fromString($values['id']), 'status' => $status] + $values);
    }
}
