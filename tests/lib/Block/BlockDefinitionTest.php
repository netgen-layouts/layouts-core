<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\PlaceholderDefinition;
use Netgen\BlockManager\Core\Values\Page\Block;
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
                'placeholders' => array(
                    'left' => new PlaceholderDefinition(array('identifier' => 'left')),
                    'right' => new PlaceholderDefinition(array('identifier' => 'right')),
                ),
                'dynamicPlaceholder' => new PlaceholderDefinition(array('identifier' => 'dynamic')),
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
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals(
            array(
                'left' => new PlaceholderDefinition(array('identifier' => 'left')),
                'right' => new PlaceholderDefinition(array('identifier' => 'right')),
            ),
            $this->blockDefinition->getPlaceholders()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getPlaceholder
     */
    public function testGetPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'left')),
            $this->blockDefinition->getPlaceholder('left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getPlaceholder
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetPlaceholderThrowsInvalidArgumentException()
    {
        $this->blockDefinition->getPlaceholder('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasPlaceholder
     */
    public function testHasPlaceholder()
    {
        $this->assertTrue($this->blockDefinition->hasPlaceholder('left'));
        $this->assertFalse($this->blockDefinition->hasPlaceholder('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicPlaceholder
     */
    public function testGetDynamicPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'dynamic')),
            $this->blockDefinition->getDynamicPlaceholder()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasCollection
     */
    public function testHasCollection()
    {
        $this->assertTrue($this->blockDefinition->hasCollection());
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
