<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use PHPUnit\Framework\TestCase;

final class ConditionUpdateStructTest extends TestCase
{
    public function testSetProperties()
    {
        $conditionUpdateStruct = new ConditionUpdateStruct(
            [
                'value' => 42,
            ]
        );

        $this->assertEquals(42, $conditionUpdateStruct->value);
    }
}
