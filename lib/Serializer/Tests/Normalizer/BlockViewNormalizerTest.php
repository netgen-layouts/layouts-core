<?php

namespace Netgen\BlockManager\Serializer\Normalizer\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class BlockViewNormalizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer::normalize
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
            'name' => 'Paragraph',
        );

        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configuration
            ->expects($this->any())
            ->method('getBlockConfig')
            ->with($this->equalTo('paragraph'))
            ->will($this->returnValue($config));

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($blockView))
            ->will($this->returnValue('rendered block view'));

        $blockViewNormalizer = new BlockViewNormalizer($configuration, $viewRendererMock);

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'name' => $config['name'],
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
     * @covers \Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\ViewRendererInterface');
        $blockViewNormalizer = new BlockViewNormalizer($configuration, $viewRendererMock);

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
