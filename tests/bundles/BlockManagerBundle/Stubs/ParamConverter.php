<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter as BaseParamConverter;

final class ParamConverter extends BaseParamConverter
{
    public function getSourceAttributeNames()
    {
        return array('id');
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
        $published = $values['published'];
        unset($values['published']);

        return new Value($values + array('status' => $published ? Value::STATUS_PUBLISHED : Value::STATUS_DRAFT));
    }
}
