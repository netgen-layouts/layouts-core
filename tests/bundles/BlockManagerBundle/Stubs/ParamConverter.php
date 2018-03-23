<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter as BaseParamConverter;

final class ParamConverter extends BaseParamConverter
{
    /**
     * Returns source attribute name.
     *
     * @return array
     */
    public function getSourceAttributeNames()
    {
        return array('id');
    }

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    public function getDestinationAttributeName()
    {
        return 'value';
    }

    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return Value::class;
    }

    /**
     * Returns the value.
     *
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValue(array $values)
    {
        return new Value($values);
    }
}
