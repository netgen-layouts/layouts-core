<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Parameters;

use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use PHPUnit\Framework\TestCase;

class ItemLinkTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ItemLink();
        $this->assertEquals('ngbm_item_link', $constraint->validatedBy());
    }
}
