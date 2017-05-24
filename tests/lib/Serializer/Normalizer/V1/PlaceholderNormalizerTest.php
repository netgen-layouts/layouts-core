<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer;
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
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setSerializer($this->serializerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer::normalize
     */
    public function testNormalize()
    {
        $placeholder = new Placeholder(
            array(
                'identifier' => 'main',
                'blocks' => array(
                    new Block(),
                ),
            )
        );

        $this->serializerMock
            ->expects($this->at(0))
            ->method('normalize')
            ->with($this->equalTo(array(new View(new Block(), 1))))
            ->will($this->returnValue(array('normalized blocks')));

        $this->assertEquals(
            array(
                'identifier' => 'main',
                'blocks' => array('normalized blocks'),
            ),
            $this->normalizer->normalize(new VersionedValue($placeholder, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\PlaceholderNormalizer::supportsNormalization
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
