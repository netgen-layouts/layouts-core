<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use PHPUnit\Framework\TestCase;

class RuleCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $ruleCreateStruct = new RuleCreateStruct();

        $this->assertNull($ruleCreateStruct->layoutId);
        $this->assertEquals(0, $ruleCreateStruct->priority);
        $this->assertFalse($ruleCreateStruct->enabled);
        $this->assertNull($ruleCreateStruct->comment);
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

        $this->assertEquals(42, $ruleCreateStruct->layoutId);
        $this->assertEquals(13, $ruleCreateStruct->priority);
        $this->assertTrue($ruleCreateStruct->enabled);
        $this->assertEquals('Comment', $ruleCreateStruct->comment);
    }
}
