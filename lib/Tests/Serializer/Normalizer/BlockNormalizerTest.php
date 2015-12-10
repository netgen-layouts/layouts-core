<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Tests\API\Stubs\Value;

class BlockNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer::normalize
     */
    public function testNormalize()
    {
        $block = new Block(
            array(
                'id' => 42,
                'zoneId' => 24,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'name' => 'My block',
            )
        );

        $blockView = new BlockView();
        $blockView->setBlock($block);

        $viewBuilderMock = $this->getMock('Netgen\BlockManager\View\ViewBuilderInterface');
        $viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo($block))
            ->will($this->returnValue($blockView));

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($blockView))
            ->will($this->returnValue('rendered block view'));

        $blockViewNormalizer = new BlockNormalizer($viewBuilderMock, $viewRendererMock);

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'name' => $block->getName(),
                'zone_id' => $block->getZoneId(),
                'parameters' => $block->getParameters(),
                'view_type' => $block->getViewType(),
                'html' => 'rendered block view',
            ),
            $blockViewNormalizer->normalize(new SerializableValue($block, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $viewBuilderMock = $this->getMock('Netgen\BlockManager\View\ViewBuilderInterface');
        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $blockViewNormalizer = new BlockNormalizer($viewBuilderMock, $viewRendererMock);

        self::assertEquals($expected, $blockViewNormalizer->supportsNormalization($data));
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
            array('block_view', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Block(), false),
            array(new SerializableValue(new Value(), 1), false),
            array(new SerializableValue(new Block(), 1), true),
        );
    }
}
