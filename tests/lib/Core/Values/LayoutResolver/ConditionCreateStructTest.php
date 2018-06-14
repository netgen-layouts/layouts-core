<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use PHPUnit\Framework\TestCase;

final class ConditionCreateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct(
            [
                'type' => 'condition',
                'value' => 42,
            ]
        );

        $this->assertEquals('condition', $conditionCreateStruct->type);
        $this->assertEquals(42, $conditionCreateStruct->value);
    }
}
