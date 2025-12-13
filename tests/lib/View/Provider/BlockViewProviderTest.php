<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\BlockViewProvider;
use Netgen\Layouts\View\View\BlockViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(BlockViewProvider::class)]
final class BlockViewProviderTest extends TestCase
{
    private BlockViewProvider $blockViewProvider;

    protected function setUp(): void
    {
        $this->blockViewProvider = new BlockViewProvider();
    }

    public function testProvideView(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::v4(),
            ],
        );

        $view = $this->blockViewProvider->provideView($block);

        self::assertInstanceOf(BlockViewInterface::class, $view);

        self::assertSame($block, $view->block);
        self::assertNull($view->template);
        self::assertSame(
            [
                'block' => $block,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(mixed $value, bool $supports): void
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
