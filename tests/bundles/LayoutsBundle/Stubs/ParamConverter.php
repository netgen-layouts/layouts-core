<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Stubs;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter as BaseParamConverter;
use Netgen\Layouts\API\Values\Value as APIValue;
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
        $status = APIValue::STATUS_DRAFT;
        if ($values['status'] === 'published') {
            $status = APIValue::STATUS_PUBLISHED;
        } elseif ($values['status'] === 'archived') {
            $status = APIValue::STATUS_ARCHIVED;
        }

        unset($values['status']);

        return Value::fromArray(['id' => Uuid::fromString($values['id']), 'status' => $status] + $values);
    }
}
