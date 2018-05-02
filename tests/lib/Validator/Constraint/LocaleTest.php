<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Locale;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Locale::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Locale();
        $this->assertEquals('ngbm_locale', $constraint->validatedBy());
    }
}
