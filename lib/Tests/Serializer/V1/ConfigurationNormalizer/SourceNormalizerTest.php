<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

class SourceNormalizerTest extends TestCase
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
        $this->serializerMock = $this->createMock(Serializer::class);

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
            'Source',
            array(
                new Query('identifier', new QueryType('ezcontent'), array('param' => 'value')),
                new Query('identifier2', new QueryType('ezcontent'), array('param2' => 'value2')),
            )
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with(
                $this->equalTo(
                    array(
                        new VersionedValue(new Query('identifier', new QueryType('ezcontent'), array('param' => 'value')), 1),
                        new VersionedValue(new Query('identifier2', new QueryType('ezcontent'), array('param2' => 'value2')), 1),
                    )
                )
            )
            ->will($this->returnValue(array('queries')));

        $this->assertEquals(
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
            array(new Source('identifier', 'name', array()), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Source('identifier', 'name', array()), 2), false),
            array(new VersionedValue(new Source('identifier', 'name', array()), 1), true),
        );
    }
}
