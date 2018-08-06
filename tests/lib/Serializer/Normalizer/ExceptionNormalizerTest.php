<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Exception;
use Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExceptionNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer
     */
    private $exceptionNormalizer;

    public function setUp(): void
    {
        $this->exceptionNormalizer = new ExceptionNormalizer(false);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $exception = new Exception('Exception message', 123);

        $this->assertSame(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ],
            $this->exceptionNormalizer->normalize($exception)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalizeWithDebugOutput(): void
    {
        $this->exceptionNormalizer = new ExceptionNormalizer(true);

        $previousException = new Exception('Previous exception', 321);
        $exception = new Exception('Exception message', 123, $previousException);
        $data = $this->exceptionNormalizer->normalize($exception);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('debug', $data);
        $this->assertArrayHasKey('line', $data['debug']);
        $this->assertArrayHasKey('file', $data['debug']);
        $this->assertArrayHasKey('trace', $data['debug']);

        $this->assertSame($exception->getCode(), $data['code']);
        $this->assertSame($exception->getMessage(), $data['message']);
        $this->assertSame(__FILE__, $data['debug']['file']);
        $this->assertGreaterThan(0, $data['debug']['line']);
        $this->assertNotEmpty($data['debug']['trace']);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalizeHttpException(): void
    {
        $exception = new NotFoundHttpException('Exception message', null, 123);

        $this->assertSame(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            ],
            $this->exceptionNormalizer->normalize($exception)
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        $this->assertSame($expected, $this->exceptionNormalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
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
        ];
    }
}
