<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Serializer\Normalizer\ValueNormalizer;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as StubValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class ValueNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\ValueNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new ValueNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ValueNormalizer::normalize
     */
    public function testNormalize()
    {
        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(new StubValue()),
                $this->equalTo('json'),
                $this->equalTo(array('context'))
            )
            ->will($this->returnValue(array('serialized')));

        $value = new Value(new StubValue());

        $data = $this->normalizer->normalize($value, 'json', array('context'));

        $this->assertEquals(array('serialized'), $data);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ValueNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
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
            array('block', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new StubValue(), false),
            array(new Block(), false),
            array(new VersionedValue(new Block(), 1), false),
            array(new Value(array(new Block()), 1), true),
        );
    }
}
