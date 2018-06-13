<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use PHPUnit\Framework\TestCase;

final class BlockViewTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\BlockViewType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockViewType();
        $this->assertEquals('ngbm_block_view_type', $constraint->validatedBy());
    }
}
