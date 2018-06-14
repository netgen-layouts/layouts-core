<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use PHPUnit\Framework\TestCase;

final class RuleUpdateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct(
            [
                'layoutId' => 42,
                'comment' => 'Comment',
            ]
        );

        $this->assertEquals(42, $ruleUpdateStruct->layoutId);
        $this->assertEquals('Comment', $ruleUpdateStruct->comment);
    }
}
