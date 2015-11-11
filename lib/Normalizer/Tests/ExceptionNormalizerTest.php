<?php

namespace Netgen\BlockManager\Normalizer\Tests;

use Netgen\BlockManager\Normalizer\ExceptionNormalizer;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPUnit_Framework_TestCase;
use Exception;

class ExceptionNormalizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Normalizer\ExceptionNormalizer::normalize
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
     * @covers \Netgen\BlockManager\Normalizer\ExceptionNormalizer::normalize
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
     * @covers \Netgen\BlockManager\Normalizer\ExceptionNormalizer::supportsNormalization
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
