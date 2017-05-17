<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

class BlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->handler = new BlockDefinitionHandler();

        $this->configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'config' => $this->configMock,
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
        $this->assertEquals(
            array(
                'definition_param' => 'definition_value',
            ),
            $this->blockDefinition->getDynamicParameters(new Block())
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->blockDefinition->getConfig());
    }
}
