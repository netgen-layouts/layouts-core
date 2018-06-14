<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\Parameters;

use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use PHPUnit\Framework\TestCase;

final class ItemLinkTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ItemLink();
        $this->assertEquals('ngbm_item_link', $constraint->validatedBy());
    }
}
