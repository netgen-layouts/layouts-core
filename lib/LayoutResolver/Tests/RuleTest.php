<?php

namespace Netgen\BlockManager\LayoutResolver\Tests;

use Netgen\BlockManager\LayoutResolver\Rule;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Target;
use PHPUnit_Framework_TestCase;

class RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule::__construct
     */
    public function testConstructor()
    {
        $layoutId = 42;
        $target = new Target();
        $rule = new Rule($layoutId, $target);

        self::assertEquals($layoutId, $rule->layoutId);
        self::assertEquals($target, $rule->target);
    }
}
