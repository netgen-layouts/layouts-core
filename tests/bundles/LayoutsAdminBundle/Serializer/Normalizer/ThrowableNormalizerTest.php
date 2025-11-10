<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Error;
use Exception;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ThrowableNormalizer;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ThrowableNormalizerTest extends TestCase
{
    private ThrowableNormalizer $throwableNormalizer;

    protected function setUp(): void
    {
        $this->throwableNormalizer = new ThrowableNormalizer(false);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ThrowableNormalizer::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ThrowableNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $exception = new Exception('Exception message', 123);

        self::assertSame(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ],
            $this->throwableNormalizer->normalize($exception),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ThrowableNormalizer::normalize
     */
    public function testNormalizeWithDebugOutput(): void
    {
        $this->throwableNormalizer = new ThrowableNormalizer(true);

        $previousException = new Exception('Previous exception', 321);
        $exception = new Exception('Exception message', 123, $previousException);
        $data = $this->throwableNormalizer->normalize($exception);

        self::assertArrayHasKey('code', $data);
        self::assertArrayHasKey('message', $data);
        self::assertArrayHasKey('debug', $data);
        self::assertArrayHasKey('line', $data['debug']);
        self::assertArrayHasKey('file', $data['debug']);
        self::assertArrayHasKey('trace', $data['debug']);

        self::assertSame($exception->getCode(), $data['code']);
        self::assertSame($exception->getMessage(), $data['message']);
        self::assertSame(__FILE__, $data['debug']['file']);
        self::assertGreaterThan(0, $data['debug']['line']);
        self::assertNotEmpty($data['debug']['trace']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ThrowableNormalizer::normalize
     */
    public function testNormalizeHttpException(): void
    {
        $exception = new NotFoundHttpException('Exception message', null, 123);

        self::assertSame(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            ],
            $this->throwableNormalizer->normalize($exception),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ThrowableNormalizer::supportsNormalization
     *
     * @dataProvider supportsNormalizationDataProvider
     */
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        self::assertSame($expected, $this->throwableNormalizer->supportsNormalization($data));
    }

    public static function supportsNormalizationDataProvider(): iterable
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['exception', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Exception(), true],
            [new Error(), true],
        ];
    }
}
