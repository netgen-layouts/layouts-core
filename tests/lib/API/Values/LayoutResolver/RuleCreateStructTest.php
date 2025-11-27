<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class RuleCreateStructTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();

        self::assertTrue($ruleCreateStruct->isEnabled);
    }
}
