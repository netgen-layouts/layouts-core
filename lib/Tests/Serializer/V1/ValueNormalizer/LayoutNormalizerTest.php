<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone as LayoutTypeZone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\LayoutNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use DateTime;

class LayoutNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\LayoutNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->layoutTypeRegistry = new LayoutTypeRegistry();

        $layoutType = new LayoutType(
            '3_zones_a',
            true,
            '3 zones A',
            array(
                'left' => new LayoutTypeZone('left', 'Left', array('title')),
                'right' => new LayoutTypeZone('right', 'Right', array()),
            )
        );

        $this->layoutTypeRegistry->addLayoutType('3_zones_a', $layoutType);
        $this->normalizer = new LayoutNormalizer($this->layoutTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\LayoutNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\LayoutNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\LayoutNormalizer::getZones
     */
    public function testNormalize()
    {
        $currentDate = new DateTime();
        $currentDate->setTimestamp(time());

        $block = new Block(
            array(
                'id' => 24,
            )
        );

        $layout = new Layout(
            array(
                'id' => 42,
                'parentId' => null,
                'type' => '3_zones_a',
                'created' => $currentDate,
                'modified' => $currentDate,
                'zones' => array(
                    'left' => new Zone(
                        array(
                            'identifier' => 'left',
                            'blocks' => array($block),
                        )
                    ),
                    'right' => new Zone(
                        array(
                            'identifier' => 'right',
                            'blocks' => array(),
                        )
                    ),
                ),
            )
        );

        $layoutView = new LayoutView();
        $layoutView->setLayout($layout);

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'type' => $layout->getType(),
                'created_at' => $layout->getCreated()->format(DateTime::ISO8601),
                'updated_at' => $layout->getModified()->format(DateTime::ISO8601),
                'name' => $layout->getName(),
                'zones' => array(
                    array(
                        'identifier' => 'left',
                        'block_ids' => array(24),
                        'allowed_block_types' => array('title'),
                    ),
                    array(
                        'identifier' => 'right',
                        'block_ids' => array(),
                        'allowed_block_types' => true,
                    ),
                ),
            ),
            $this->normalizer->normalize(new VersionedValue($layout, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\LayoutNormalizer::supportsNormalization
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
            array('layout', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Layout(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new Layout(), 2), false),
            array(new VersionedValue(new Layout(), 1), true),
        );
    }
}
