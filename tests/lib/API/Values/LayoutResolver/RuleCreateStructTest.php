<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use PHPUnit\Framework\TestCase;

final class RuleCreateStructTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();

        self::assertFalse($ruleCreateStruct->enabled);
    }
}
