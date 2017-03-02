<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\PlaceholderNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class PlaceholderNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\PlaceholderNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\PlaceholderNormalizer::normalize
     */
    public function testNormalize()
    {
        $placeholder = new Placeholder(
            array(
                'identifier' => 'main',
                'blocks' => array(
                    new Block(),
                ),
                'parameters' => array(
                    'some_param' => new ParameterValue(
                        array(
                            'name' => 'some_param',
                            'value' => 'some_value',
                        )
                    ),
                    'some_other_param' => new ParameterValue(
                        array(
                            'name' => 'some_other_param',
                            'value' => 'some_other_value',
                        )
                    ),
                ),
            )
        );

        $serializedParams = array(
            'some_param' => 'some_value',
            'some_other_param' => 'some_other_value',
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->will($this->returnValue($serializedParams));

        $this->serializerMock
            ->expects($this->at(1))
            ->method('normalize')
            ->with($this->equalTo(array(new View(new Block(), 1))))
            ->will($this->returnValue(array('normalized blocks')));

        $this->assertEquals(
            array(
                'identifier' => 'main',
                'parameters' => $serializedParams,
                'blocks' => array('normalized blocks'),
            ),
            $this->normalizer->normalize(new VersionedValue($placeholder, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\PlaceholderNormalizer::supportsNormalization
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
            array('placeholder', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Placeholder(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Placeholder(), 2), false),
            array(new VersionedValue(new Placeholder(), 1), true),
        );
    }
}
