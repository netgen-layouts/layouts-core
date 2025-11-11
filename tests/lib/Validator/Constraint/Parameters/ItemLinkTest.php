<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Parameters;

use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemLink::class)]
final class ItemLinkTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new ItemLink();
        self::assertSame('nglayouts_item_link', $constraint->validatedBy());
    }
}
