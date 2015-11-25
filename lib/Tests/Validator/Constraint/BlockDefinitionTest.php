<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\BlockDefinition;

class BlockDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\BlockDefinition::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockDefinition();
        self::assertEquals('ngbm_block_definition', $constraint->validatedBy());
    }
}
