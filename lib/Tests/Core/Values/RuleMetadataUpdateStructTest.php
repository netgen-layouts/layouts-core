<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\RuleMetadataUpdateStruct;
use PHPUnit\Framework\TestCase;

class RuleMetadataUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $ruleUpdateStruct = new RuleMetadataUpdateStruct();

        self::assertNull($ruleUpdateStruct->priority);
    }

    public function testSetProperties()
    {
        $ruleUpdateStruct = new RuleMetadataUpdateStruct(
            array(
                'priority' => 42,
            )
        );

        self::assertEquals(42, $ruleUpdateStruct->priority);
    }
}
