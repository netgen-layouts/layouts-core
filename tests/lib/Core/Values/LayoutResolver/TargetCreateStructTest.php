<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use PHPUnit\Framework\TestCase;

final class TargetCreateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $targetCreateStruct = new TargetCreateStruct(
            [
                'type' => 'target',
                'value' => 42,
            ]
        );

        $this->assertSame('target', $targetCreateStruct->type);
        $this->assertSame(42, $targetCreateStruct->value);
    }
}
