<?php

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\TranslationException;
use PHPUnit\Framework\TestCase;

class TranslationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\TranslationException::noTranslation
     */
    public function testNoTranslation()
    {
        $exception = TranslationException::noTranslation('en');

        $this->assertEquals(
            'Translation with "en" locale does not exist.',
            $exception->getMessage()
        );
    }
}
