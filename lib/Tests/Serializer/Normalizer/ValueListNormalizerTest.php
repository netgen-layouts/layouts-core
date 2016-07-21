<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\Normalizer\ValueListNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

class ValueListNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\ValueListNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new ValueListNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ValueListNormalizer::normalize
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
            ->with($this->equalTo(new ValueList(array('param' => 'value'))))
            ->will($this->returnValue(array('param' => 'value')));

        $valueList = new ValueList(
            array(
                'value' => new VersionedValue(new Value(), 1),
                'array' => array('param' => 'value'),
                'id' => 42,
            )
        );

        $data = $this->normalizer->normalize($valueList);

        $this->assertEquals(
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
     * @covers \Netgen\BlockManager\Serializer\Normalizer\ValueListNormalizer::supportsNormalization
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
            array(new Value(), false),
            array(new Block(), false),
            array(new VersionedValue(new Block(), 1), false),
            array(new ValueList(array(new Block()), 1), true),
        );
    }
}
