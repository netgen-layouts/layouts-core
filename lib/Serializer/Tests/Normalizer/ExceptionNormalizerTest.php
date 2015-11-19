<?php

namespace Netgen\BlockManager\Serializer\Normalizer\Tests;

use Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPUnit_Framework_TestCase;
use Exception;

class ExceptionNormalizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalize()
    {
        $exceptionNormalizer = new ExceptionNormalizer();

        $exception = new Exception('Exception message', 123);

        self::assertEquals(
            array(
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ),
            $exceptionNormalizer->normalize($exception)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::setOutputDebugInfo
     */
    public function testNormalizeWithDebugOutput()
    {
        $exceptionNormalizer = new ExceptionNormalizer();
        $exceptionNormalizer->setOutputDebugInfo(true);

        $exception = new Exception('Exception message', 123);
        $data = $exceptionNormalizer->normalize($exception);

        self::assertInternalType('array', $data);
        self::assertArrayHasKey('code', $data);
        self::assertArrayHasKey('message', $data);
        self::assertArrayHasKey('debug', $data);
        self::assertArrayHasKey('line', $data['debug']);
        self::assertArrayHasKey('file', $data['debug']);
        self::assertArrayHasKey('trace', $data['debug']);

        self::assertEquals($exception->getCode(), $data['code']);
        self::assertEquals($exception->getMessage(), $data['message']);
        self::assertEquals(__FILE__, $data['debug']['file']);
        self::assertGreaterThan(0, $data['debug']['line']);
        self::assertNotEmpty($data['debug']['trace']);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalizeHttpException()
    {
        $exceptionNormalizer = new ExceptionNormalizer();

        $exception = new NotFoundHttpException('Exception message', null, 123);

        self::assertEquals(
            array(
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            ),
            $exceptionNormalizer->normalize($exception)
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $exceptionNormalizer = new ExceptionNormalizer();
        self::assertEquals($expected, $exceptionNormalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('exception', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Exception(), true),
        );
    }
}
