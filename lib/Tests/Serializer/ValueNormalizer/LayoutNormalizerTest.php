<?php

namespace Netgen\BlockManager\Tests\Serializer\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\ValueNormalizer\LayoutNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use DateTime;

class LayoutNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \Netgen\BlockManager\Serializer\ValueNormalizer\LayoutNormalizer
     */
    protected $layoutNormalizer;

    public function setUp()
    {
        $this->configurationMock = $this->getMock(ConfigurationInterface::class);

        $layoutConfig = array(
            'zones' => array(
                'left' => array(
                    'name' => 'Left',
                    'allowed_block_types' => array('title'),
                ),
                'right' => array(
                    'name' => 'Right',
                ),
            ),
        );

        $this->configurationMock
            ->expects($this->any())
            ->method('getLayoutConfig')
            ->with($this->equalTo('3_zones_a'))
            ->will($this->returnValue($layoutConfig));

        $this->layoutNormalizer = new LayoutNormalizer($this->configurationMock);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\LayoutNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\LayoutNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\LayoutNormalizer::getZones
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
            $this->layoutNormalizer->normalize(new VersionedValue($layout, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\ValueNormalizer\LayoutNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        self::assertEquals($expected, $this->layoutNormalizer->supportsNormalization($data));
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
