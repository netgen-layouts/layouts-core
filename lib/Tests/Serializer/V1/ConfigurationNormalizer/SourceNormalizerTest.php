<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceNormalizer;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Serializer\Serializer;

class SourceNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->getMock(Serializer::class);

        $this->normalizer = new SourceNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceNormalizer::normalize
     */
    public function testNormalize()
    {
        $source = new Source(
            'identifier',
            true,
            'Source',
            array(
                new Query('identifier', 'ezcontent', array('param' => 'value')),
                new Query('identifier2', 'ezcontent', array('param2' => 'value2')),
            )
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    new ValueArray(
                        array(
                            new VersionedValue(new Query('identifier', 'ezcontent', array('param' => 'value')), 1),
                            new VersionedValue(new Query('identifier2', 'ezcontent', array('param2' => 'value2')), 1),
                        )
                    )
                )
            )
            ->will($this->returnValue(array('queries')));

        self::assertEquals(
            array(
                'identifier' => $source->getIdentifier(),
                'name' => $source->getName(),
                'queries' => array('queries'),
            ),
            $this->normalizer->normalize(new VersionedValue($source, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceNormalizer::supportsNormalization
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
            array(new Source('identifier', true, 'name', array()), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Source('identifier', true, 'name', array()), 2), false),
            array(new VersionedValue(new Source('identifier', true, 'name', array()), 1), true),
        );
    }
}
