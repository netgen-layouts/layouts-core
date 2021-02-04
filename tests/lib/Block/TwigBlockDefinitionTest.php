<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\TwigBlockDefinition;
use Netgen\Layouts\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class TwigBlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\Layouts\Block\TwigBlockDefinition
     */
    private $blockDefinition;

    protected function setUp(): void
    {
        $this->handler = new TwigBlockDefinitionHandler();

        $this->blockDefinition = TwigBlockDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\TwigBlockDefinition::getTwigBlockNames
     */
    public function testGetTwigBlockNames(): void
    {
        self::assertSame(['twig_block'], $this->blockDefinition->getTwigBlockNames(new Block()));
    }

    /**
     * @covers \Netgen\Layouts\Block\TwigBlockDefinition::getDynamicParameters
     * @covers \Netgen\Layouts\Block\TwigBlockDefinition::getHandler
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\Layouts\Block\TwigBlockDefinition::getHandler
     * @covers \Netgen\Layouts\Block\TwigBlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertTrue($this->blockDefinition->isContextual(new Block()));
    }
}
