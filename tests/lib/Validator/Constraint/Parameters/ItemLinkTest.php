<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Parameters;

use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink;
use PHPUnit\Framework\TestCase;

final class ItemLinkTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Parameters\ItemLink::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ItemLink();
        self::assertSame('nglayouts_item_link', $constraint->validatedBy());
    }
}
