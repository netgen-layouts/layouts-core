<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter as BaseParamConverter;

final class ParamConverter extends BaseParamConverter
{
    public function getSourceAttributeNames()
    {
        return ['id'];
    }

    public function getDestinationAttributeName()
    {
        return 'value';
    }

    public function getSupportedClass()
    {
        return Value::class;
    }

    public function loadValue(array $values)
    {
        $status = Value::STATUS_DRAFT;
        if ($values['status'] === 'published') {
            $status = Value::STATUS_PUBLISHED;
        } elseif ($values['status'] === 'archived') {
            $status = Value::STATUS_ARCHIVED;
        }

        unset($values['status']);

        return new Value($values + ['status' => $status]);
    }
}
