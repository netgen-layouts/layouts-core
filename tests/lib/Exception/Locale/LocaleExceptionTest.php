<?php

namespace Netgen\BlockManager\Tests\Exception\Locale;

use Netgen\BlockManager\Exception\Locale\LocaleException;
use PHPUnit\Framework\TestCase;

class LocaleExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Locale\LocaleException::noLocale
     */
    public function testNoLocale()
    {
        $exception = LocaleException::noLocale();

        $this->assertEquals(
            'No locales available in the current context.',
            $exception->getMessage()
        );
    }
}
