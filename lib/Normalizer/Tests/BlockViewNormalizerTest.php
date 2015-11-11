<?php

namespace Netgen\BlockManager\Normalizer\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Normalizer\BlockViewNormalizer;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class BlockViewNormalizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Normalizer\BlockViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Normalizer\BlockViewNormalizer::normalize
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
            )
        );

        $blockView = new BlockView();
        $blockView->setBlock($block);

        $config = array(
            'paragraph' => array(
                'name' => 'Paragraph',
            ),
        );

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($blockView))
            ->will($this->returnValue('rendered block view'));

        $blockViewNormalizer = new BlockViewNormalizer($config, $viewRendererMock);

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'title' => $config[$block->getDefinitionIdentifier()]['name'],
                'zone_id' => $block->getZoneId(),
                'parameters' => $block->getParameters(),
                'view_type' => $block->getViewType(),
                'html' => 'rendered block view',
            ),
            $blockViewNormalizer->normalize($blockView)
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Normalizer\BlockViewNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $blockViewNormalizer = new BlockViewNormalizer(array(), $viewRendererMock);

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
            array(new BlockView(), true),
        );
    }
}
