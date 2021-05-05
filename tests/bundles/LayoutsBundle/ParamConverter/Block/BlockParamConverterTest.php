<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Block;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class BlockParamConverterTest extends TestCase
{
    private MockObject $blockServiceMock;

    private BlockParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->paramConverter = new BlockParamConverter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['blockId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('block', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Block::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $block = new Block();

        $uuid = Uuid::uuid4();

        $this->blockServiceMock
            ->expects(self::once())
            ->method('loadBlock')
            ->with(self::equalTo($uuid))
            ->willReturn($block);

        self::assertSame(
            $block,
            $this->paramConverter->loadValue(
                [
                    'blockId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Block\BlockParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $block = new Block();

        $uuid = Uuid::uuid4();

        $this->blockServiceMock
            ->expects(self::once())
            ->method('loadBlockDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($block);

        self::assertSame(
            $block,
            $this->paramConverter->loadValue(
                [
                    'blockId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
