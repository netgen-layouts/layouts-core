<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\LayoutName;
use PHPUnit\Framework\TestCase;

final class LayoutNameTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\LayoutName::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new LayoutName();
        $this->assertEquals('ngbm_layout_name', $constraint->validatedBy());
    }
}
