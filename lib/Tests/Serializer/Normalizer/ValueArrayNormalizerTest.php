<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\Normalizer\ValueArrayNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Serializer\Serializer;

class ValueArrayNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\ValueArrayNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->getMock(Serializer::class);

        $this->normalizer = new ValueArrayNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ValueArrayNormalizer::normalize
     */
    public function testNormalize()
    {
        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with($this->equalTo(new VersionedValue(new Value(), 1)))
            ->will($this->returnValue(array('value_id' => 24)));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with($this->equalTo(new ValueArray(array('param' => 'value'))))
            ->will($this->returnValue(array('param' => 'value')));

        $valueArray = new ValueArray(
            array(
                'value' => new VersionedValue(new Value(), 1),
                'array' => array('param' => 'value'),
                'id' => 42,
            )
        );

        $data = $this->normalizer->normalize($valueArray);

        self::assertEquals(
            array(
                'value' => array('value_id' => 24),
                'array' => array('param' => 'value'),
                'id' => 42,
            ),
            $data
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ValueArrayNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        self::assertEquals($expected, $this->normalizer->supportsNormalization($data));
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
            array(new Value(), false),
            array(new Block(), false),
            array(new VersionedValue(new Block(), 1), false),
            array(new ValueArray(new Block(), 1), false),
            array(new ValueArray(array(new Block()), 1), true),
        );
    }
}
