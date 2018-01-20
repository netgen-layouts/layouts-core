<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $rule = new Rule();

        $this->assertNull($rule->id);
        $this->assertNull($rule->layoutId);
        $this->assertNull($rule->priority);
        $this->assertNull($rule->enabled);
        $this->assertNull($rule->comment);
        $this->assertNull($rule->status);
    }

    public function testSetProperties()
    {
        $rule = new Rule(
            array(
                'id' => 43,
                'layoutId' => 25,
                'enabled' => true,
                'priority' => 3,
                'comment' => 'Comment',
                'status' => Value::STATUS_DRAFT,
            )
        );

        $this->assertEquals(43, $rule->id);
        $this->assertEquals(25, $rule->layoutId);
        $this->assertTrue($rule->enabled);
        $this->assertEquals(3, $rule->priority);
        $this->assertEquals('Comment', $rule->comment);
        $this->assertEquals(Value::STATUS_DRAFT, $rule->status);
    }
}
