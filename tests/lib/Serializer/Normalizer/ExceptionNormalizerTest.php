<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Exception;
use Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer
     */
    protected $exceptionNormalizer;

    public function setUp()
    {
        $this->exceptionNormalizer = new ExceptionNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalize()
    {
        $exception = new Exception('Exception message', 123);

        $this->assertEquals(
            array(
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ),
            $this->exceptionNormalizer->normalize($exception)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::setOutputDebugInfo
     */
    public function testNormalizeWithDebugOutput()
    {
        $this->exceptionNormalizer->setOutputDebugInfo(true);

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

        $this->assertEquals($exception->getCode(), $data['code']);
        $this->assertEquals($exception->getMessage(), $data['message']);
        $this->assertEquals(__FILE__, $data['debug']['file']);
        $this->assertGreaterThan(0, $data['debug']['line']);
        $this->assertNotEmpty($data['debug']['trace']);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ExceptionNormalizer::normalize
     */
    public function testNormalizeHttpException()
    {
        $exception = new NotFoundHttpException('Exception message', null, 123);

        $this->assertEquals(
            array(
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            ),
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
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->exceptionNormalizer->supportsNormalization($data));
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
