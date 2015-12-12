<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use DateTime;

class LayoutNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer::getZones
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutNormalizer::getBlockPositions
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
                'identifier' => '3_zones_a',
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

        $layoutConfig = array(
            'zones' => array(
                'left' => array(
                    'name' => 'Left',
                    'allowed_blocks' => array('title'),
                ),
                'right' => array(
                    'name' => 'Right',
                ),
            ),
        );

        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getLayoutConfig')
            ->with($this->equalTo('3_zones_a'))
            ->will($this->returnValue($layoutConfig));

        $layoutNormalizer = new LayoutNormalizer($configurationMock);

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'identifier' => $layout->getIdentifier(),
                'created_at' => $layout->getCreated(),
                'updated_at' => $layout->getModified(),
                'name' => $layout->getName(),
                'zones' => array(
                    array(
                        'identifier' => 'left',
                        'allowed_blocks' => array('title'),
                    ),
                    array(
                        'identifier' => 'right',
                        'allowed_blocks' => true,
                    ),
                ),
                'positions' => array(
                    array(
                        'zone' => 'left',
                        'blocks' => array(24),
                    ),
                    array(
                        'zone' => 'right',
                        'blocks' => array(),
                    ),
                ),
            ),
            $layoutNormalizer->normalize(new SerializableValue($layout, 1))
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
        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');

        $layoutNormalizer = new LayoutNormalizer($configurationMock);

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
            array(new Layout(), false),
            array(new SerializableValue(new Value(), 1), false),
            array(new SerializableValue(new Layout(), 2), false),
            array(new SerializableValue(new Layout(), 1), true),
        );
    }
}
