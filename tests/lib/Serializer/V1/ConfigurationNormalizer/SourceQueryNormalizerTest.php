<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Collection\Source\Query;
use Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer\SourceQueryNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class SourceQueryNormalizerTest extends TestCase
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
            array(
                'identifier' => 'identifier',
                'queryType' => new QueryType('type'),
                'defaultParameters' => array('param' => 'value'),
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $sourceQuery->getIdentifier(),
                'query_type' => $sourceQuery->getQueryType()->getType(),
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
            array(new Query(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Query(), 2), false),
            array(new VersionedValue(new Query(), 1), true),
        );
    }
}
