<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Block;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Block\BlockValueResolver;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
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

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $block = Block::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->blockServiceStub
            ->method('loadBlockDraft')
            ->willReturn($block);

        $request = Request::create('/');
        $request->attributes->set('blockId', $uuid->toString());

        $argument = new ArgumentMetadata('block', Block::class, false, false, null);

        self::assertSame(
            [$block],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $block = Block::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->blockServiceStub
            ->method('loadBlock')
            ->willReturn($block);

        $request = Request::create('/');
        $request->attributes->set('blockId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('block', Block::class, false, false, null);

        self::assertSame(
            [$block],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('block', Block::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('blockId', '42');

        $argument = new ArgumentMetadata('invalid', Block::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('blockId', '42');

        $argument = new ArgumentMetadata('block', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
