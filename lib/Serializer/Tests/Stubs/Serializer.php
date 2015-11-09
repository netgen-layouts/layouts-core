<?php

namespace Netgen\BlockManager\Serializer\Tests\Stubs;

use Netgen\BlockManager\Serializer\Serializer as BaseSerializer;

class Serializer extends BaseSerializer
{
    /**
     * Returns the data that will be serialized.
     *
     * @param mixed $value
     *
     * @return array
     */
    public function getValueData($value)
    {
        return array(
            'some_property' => $value->someProperty,
            'some_other_property' => $value->someOtherProperty,
        );
    }
}
