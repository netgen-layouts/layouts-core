<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\TranslationException;
use PHPUnit\Framework\TestCase;

final class TranslationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\TranslationException::noTranslation
     */
    public function testNoTranslation(): void
    {
        $exception = TranslationException::noTranslation('en');

        $this->assertSame(
            'Translation with "en" locale does not exist.',
            $exception->getMessage()
        );
    }
}
