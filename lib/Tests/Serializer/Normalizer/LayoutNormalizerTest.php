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
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::normalize
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::normalizeBlocks
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::getZones
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::getBlockPositions
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

        $normalizedBlock = array(
            'id' => 24,
            'html' => 'rendered block',
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

        $viewBuilderMock = $this->getMock('Netgen\BlockManager\View\ViewBuilderInterface');
        $viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo($layout))
            ->will($this->returnValue($layoutView));

        $blockNormalizerMock = $this
            ->getMockBuilder('Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer')
            ->disableOriginalConstructor()
            ->getMock();

        $blockNormalizerMock
            ->expects($this->once())
            ->method('normalize')
            ->with($this->equalTo(new SerializableValue($block, 1)))
            ->will($this->returnValue($normalizedBlock));

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($layoutView))
            ->will($this->returnValue('rendered layout view'));

        $layoutViewNormalizer = new LayoutNormalizer(
            $configurationMock,
            $blockNormalizerMock,
            $viewBuilderMock,
            $viewRendererMock
        );

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'identifier' => $layout->getIdentifier(),
                'created_at' => $layout->getCreated(),
                'updated_at' => $layout->getModified(),
                'name' => $layout->getName(),
                'html' => 'rendered layout view',
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
                'blocks' => array($normalizedBlock),
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
            $layoutViewNormalizer->normalize(new SerializableValue($layout, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');

        $blockNormalizerMock = $this
            ->getMockBuilder('Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer')
            ->disableOriginalConstructor()
            ->getMock();

        $viewBuilderMock = $this->getMock('Netgen\BlockManager\View\ViewBuilderInterface');
        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');

        $layoutViewNormalizer = new LayoutNormalizer(
            $configurationMock,
            $blockNormalizerMock,
            $viewBuilderMock,
            $viewRendererMock
        );

        self::assertEquals($expected, $layoutViewNormalizer->supportsNormalization($data));
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
            array('layout_view', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Layout(), false),
            array(new SerializableValue(new Value(), 1), false),
            array(new SerializableValue(new Layout(), 1), true),
        );
    }
}
