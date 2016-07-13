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
        self::assertNull($ruleUpdateStruct->comment);
    }

    public function testSetProperties()
    {
        $ruleUpdateStruct = new RuleUpdateStruct(
            array(
                'layoutId' => 42,
                'comment' => 'Comment',
            )
        );

        self::assertEquals(42, $ruleUpdateStruct->layoutId);
        self::assertEquals('Comment', $ruleUpdateStruct->comment);
    }
}
