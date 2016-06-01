<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\RuleCreateStruct;

class RuleCreateStructTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultProperties()
    {
        $ruleCreateStruct = new RuleCreateStruct();

        self::assertNull($ruleCreateStruct->layoutId);
        self::assertEquals(0, $ruleCreateStruct->priority);
        self::assertFalse($ruleCreateStruct->enabled);
        self::assertNull($ruleCreateStruct->comment);
    }

    public function testSetProperties()
    {
        $ruleCreateStruct = new RuleCreateStruct(
            array(
                'layoutId' => 42,
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
            )
        );

        self::assertEquals(42, $ruleCreateStruct->layoutId);
        self::assertEquals(13, $ruleCreateStruct->priority);
        self::assertTrue($ruleCreateStruct->enabled);
        self::assertEquals('Comment', $ruleCreateStruct->comment);
    }
}
