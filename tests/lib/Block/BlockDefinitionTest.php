<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;

class BlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->handler = new BlockDefinitionHandler(array(), true);

        $this->blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'handlerPlugins' => array(
                    new HandlerPlugin(),
                ),
                'config' => new Configuration(),
                'configDefinitions' => array(42),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('block_definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $dynamicParameters = new DynamicParameters();
        $dynamicParameters['definition_param'] = 'definition_value';
        $dynamicParameters['closure_param'] = function () {
            return 'closure_value';
        };

        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        $this->assertCount(3, $dynamicParameters);

        $this->assertArrayHasKey('definition_param', $dynamicParameters);
        $this->assertArrayHasKey('closure_param', $dynamicParameters);
        $this->assertArrayHasKey('dynamic_param', $dynamicParameters);

        $this->assertEquals('definition_value', $dynamicParameters['definition_param']);
        $this->assertEquals('closure_value', $dynamicParameters['closure_param']);
        $this->assertEquals('dynamic_value', $dynamicParameters['dynamic_param']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isContextual
     */
    public function testIsContextual()
    {
        $this->assertTrue($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals(new Configuration(), $this->blockDefinition->getConfig());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions()
    {
        $this->assertEquals(array(42), $this->blockDefinition->getConfigDefinitions());
    }
}
