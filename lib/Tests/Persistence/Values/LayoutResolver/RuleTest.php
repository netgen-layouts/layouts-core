<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefaultProperties()
    {
        $rule = new Rule();

        self::assertNull($rule->id);
        self::assertNull($rule->layoutId);
        self::assertNull($rule->priority);
        self::assertNull($rule->enabled);
        self::assertNull($rule->comment);
        self::assertNull($rule->status);
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
                'status' => Rule::STATUS_DRAFT,
            )
        );

        self::assertEquals(43, $rule->id);
        self::assertEquals(25, $rule->layoutId);
        self::assertTrue($rule->enabled);
        self::assertEquals(3, $rule->priority);
        self::assertEquals('Comment', $rule->comment);
        self::assertEquals(Rule::STATUS_DRAFT, $rule->status);
    }
}
