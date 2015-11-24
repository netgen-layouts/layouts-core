<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use DateTime;

class LayoutNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer::normalize
     */
    public function testNormalize()
    {
        $layoutNormalizer = new LayoutNormalizer();

        $currentDate = new DateTime();
        $currentDate->setTimestamp(time());

        $layout = new Layout(
            array(
                'id' => 42,
                'parentId' => 24,
                'identifier' => '3_zones_a',
                'name' => 'My layout',
                'created' => $currentDate,
                'modified' => $currentDate,
            )
        );

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'identifier' => $layout->getIdentifier(),
                'created_at' => $layout->getCreated(),
                'updated_at' => $layout->getModified(),
                'name' => $layout->getName(),
            ),
            $layoutNormalizer->normalize($layout)
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $layoutNormalizer = new LayoutNormalizer();

        self::assertEquals($expected, $layoutNormalizer->supportsNormalization($data));
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
            array('layout', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Layout(), true),
        );
    }
}
