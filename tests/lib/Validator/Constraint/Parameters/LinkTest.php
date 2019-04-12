<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Parameters;

use Netgen\Layouts\Validator\Constraint\Parameters\Link;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Parameters\Link::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Link();
        self::assertSame('nglayouts_link', $constraint->validatedBy());
    }
}
