<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\RuleGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RuleGroup::class)]
final class RuleGroupTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new RuleGroup();
        self::assertSame('nglayouts_rule_group', $constraint->validatedBy());
    }
}
