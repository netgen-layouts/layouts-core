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
     * @covers \Netgen\Layouts\Block\TwigBlockDefinition::getTwigBlockName
     */
    public function testGetTwigBlockName(): void
    {
        self::assertSame('twig_block', $this->blockDefinition->getTwigBlockName(new Block()));
    }
}
