<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Parameters;

use Netgen\Layouts\Validator\Constraint\Parameters\Link;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Link::class)]
final class LinkTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new Link();
        self::assertSame('nglayouts_link', $constraint->validatedBy());
    }
}
