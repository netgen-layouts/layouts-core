<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use PHPUnit\Framework\TestCase;

final class RuleMetadataUpdateStructTest extends TestCase
{
    public function testSetProperties()
    {
        $ruleUpdateStruct = new RuleMetadataUpdateStruct(
            [
                'priority' => 42,
            ]
        );

        $this->assertEquals(42, $ruleUpdateStruct->priority);
    }
}
