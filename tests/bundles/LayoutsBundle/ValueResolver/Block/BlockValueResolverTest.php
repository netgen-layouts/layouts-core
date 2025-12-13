<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Block;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Block\BlockValueResolver;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(BlockValueResolver::class)]
final class BlockValueResolverTest extends TestCase
{
    private Stub&BlockService $blockServiceStub;

    private BlockValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->blockServiceStub = self::createStub(BlockService::class);

        $this->valueResolver = new BlockValueResolver($this->blockServiceStub);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['blockId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('block', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Block::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $block = new Block();

        $uuid = Uuid::v4();

        $this->blockServiceStub
            ->method('loadBlock')
            ->with(self::equalTo($uuid))
            ->willReturn($block);

        self::assertSame(
            $block,
            $this->valueResolver->loadValue(
                [
                    'blockId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $block = new Block();

        $uuid = Uuid::v4();

        $this->blockServiceStub
            ->method('loadBlockDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($block);

        self::assertSame(
            $block,
            $this->valueResolver->loadValue(
                [
                    'blockId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
