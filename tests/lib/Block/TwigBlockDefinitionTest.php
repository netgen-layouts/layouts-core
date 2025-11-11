<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\TwigBlockDefinition;
use Netgen\Layouts\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TwigBlockDefinition::class)]
final class TwigBlockDefinitionTest extends TestCase
{
    private TwigBlockDefinitionHandler $handler;

    private TwigBlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->handler = new TwigBlockDefinitionHandler();

        $this->blockDefinition = TwigBlockDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ],
        );
    }

    public function testGetTwigBlockNames(): void
    {
        self::assertSame(['twig_block'], $this->blockDefinition->getTwigBlockNames(new Block()));
    }

    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(0, $dynamicParameters);
    }

    public function testIsContextual(): void
    {
        self::assertTrue($this->blockDefinition->isContextual(new Block()));
    }
}
