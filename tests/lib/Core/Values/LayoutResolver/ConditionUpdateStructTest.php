<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use PHPUnit\Framework\TestCase;

final class ConditionUpdateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $conditionUpdateStruct = new ConditionUpdateStruct(
            [
                'value' => 42,
            ]
        );

        $this->assertSame(42, $conditionUpdateStruct->value);
    }
}
