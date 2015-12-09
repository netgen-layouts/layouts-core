<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Normalizer\LayoutViewNormalizer;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\SerializableView;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use DateTime;

class LayoutViewNormalizerTest extends \PHPUnit_Framework_TestCase
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

        $blockView = new BlockView();
        $blockView->setBlock($block);

        $normalizedBlockView = array(
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
            ->with($this->equalTo($block))
            ->will($this->returnValue($blockView));

        $blockViewNormalizerMock = $this
            ->getMockBuilder('Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer')
            ->disableOriginalConstructor()
            ->getMock();

        $blockViewNormalizerMock
            ->expects($this->once())
            ->method('normalize')
            ->with($this->equalTo(new SerializableView($blockView, 1)))
            ->will($this->returnValue($normalizedBlockView));

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($layoutView))
            ->will($this->returnValue('rendered layout view'));

        $layoutViewNormalizer = new LayoutViewNormalizer(
            $configurationMock,
            $viewBuilderMock,
            $blockViewNormalizerMock,
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
                'blocks' => array($normalizedBlockView),
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
            $layoutViewNormalizer->normalize(new SerializableView($layoutView, 1))
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

        $viewBuilderMock = $this->getMock('Netgen\BlockManager\View\ViewBuilderInterface');

        $blockViewNormalizerMock = $this
            ->getMockBuilder('Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer')
            ->disableOriginalConstructor()
            ->getMock();

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');

        $layoutViewNormalizer = new LayoutViewNormalizer(
            $configurationMock,
            $viewBuilderMock,
            $blockViewNormalizerMock,
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
            array(new LayoutView(), false),
            array(new SerializableView(new View(), 1), false),
            array(new SerializableView(new LayoutView(), 1), true),
        );
    }
}
