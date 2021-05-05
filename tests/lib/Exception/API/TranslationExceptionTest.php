<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\API;

use Netgen\Layouts\Exception\API\TranslationException;
use PHPUnit\Framework\TestCase;

final class TranslationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\API\TranslationException::noTranslation
     */
    public function testNoTranslation(): void
    {
        $exception = TranslationException::noTranslation('en');

        self::assertSame(
            'Translation with "en" locale does not exist.',
            $exception->getMessage(),
        );
    }
}
