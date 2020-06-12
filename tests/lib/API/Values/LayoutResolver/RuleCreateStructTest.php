<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use PHPUnit\Framework\TestCase;

final class RuleCreateStructTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testDefaultProperties(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();

        self::assertTrue($ruleCreateStruct->enabled);
    }
}
