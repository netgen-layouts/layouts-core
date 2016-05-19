<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceQueryNormalizer;
use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;

class SourceQueryNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceQueryNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new SourceQueryNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceQueryNormalizer::normalize
     */
    public function testNormalize()
    {
        $sourceQuery = new Query(
            'identifier',
            'type',
            array('param' => 'value')
        );

        self::assertEquals(
            array(
                'identifier' => $sourceQuery->getIdentifier(),
                'query_type' => $sourceQuery->getQueryType(),
                'default_parameters' => $sourceQuery->getDefaultParameters(),
            ),
            $this->normalizer->normalize(new VersionedValue($sourceQuery, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceQueryNormalizer::supportsNormalization
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
            array(new Query('identifier', 'type', array()), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Query('identifier', 'type', array()), 2), false),
            array(new VersionedValue(new Query('identifier', 'type', array()), 1), true),
        );
    }
}
