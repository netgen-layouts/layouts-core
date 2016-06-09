<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use PHPUnit\Framework\TestCase;

class RuleUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $ruleUpdateStruct = new RuleUpdateStruct();

        self::assertNull($ruleUpdateStruct->layoutId);
        self::assertEquals(0, $ruleUpdateStruct->priority);
        self::assertNull($ruleUpdateStruct->comment);
    }

    public function testSetProperties()
    {
        $ruleUpdateStruct = new RuleUpdateStruct(
            array(
                'layoutId' => 42,
                'priority' => 13,
                'comment' => 'Comment',
            )
        );

        self::assertEquals(42, $ruleUpdateStruct->layoutId);
        self::assertEquals(13, $ruleUpdateStruct->priority);
        self::assertEquals('Comment', $ruleUpdateStruct->comment);
    }
}
