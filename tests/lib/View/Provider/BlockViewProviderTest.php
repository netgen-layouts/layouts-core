<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\BlockViewProvider;
use Netgen\Layouts\View\View\BlockViewInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class BlockViewProviderTest extends TestCase
{
    private BlockViewProvider $blockViewProvider;

    protected function setUp(): void
    {
        $this->blockViewProvider = new BlockViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
            ],
        );

        $view = $this->blockViewProvider->provideView($block);

        self::assertInstanceOf(BlockViewInterface::class, $view);

        self::assertSame($block, $view->getBlock());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'block' => $block,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\BlockViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->blockViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Block(), true],
            [new Layout(), false],
        ];
    }
}
