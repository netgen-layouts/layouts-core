<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Parameters;

use Netgen\BlockManager\Validator\Constraint\Parameters\Link;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Parameters\Link::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Link();
        $this->assertEquals('ngbm_link', $constraint->validatedBy());
    }
}
