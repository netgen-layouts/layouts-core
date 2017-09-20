<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\Normalizer\V1\ParameterValueNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class ParameterValueNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\ParameterValueNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new ParameterValueNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ParameterValueNormalizer::normalize
     */
    public function testNormalize()
    {
        $parameterValue = new ParameterValue(
            array(
                'parameter' => new Parameter(
                    array(
                        'type' => new TextType(),
                    )
                ),
                'value' => 'some text',
            )
        );

        $this->assertEquals(
            'some text',
            $this->normalizer->normalize(new VersionedValue($parameterValue, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\ParameterValueNormalizer::supportsNormalization
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
            array('value', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new ParameterValue(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new ParameterValue(), 2), false),
            array(new VersionedValue(new ParameterValue(), 1), true),
        );
    }
}
