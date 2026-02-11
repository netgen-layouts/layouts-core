<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleGroupValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleGroupValueResolver::class)]
final class RuleGroupValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleGroupValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleGroupValueResolver($this->layoutResolverServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $ruleGroup = RuleGroup::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroupDraft')
            ->willReturn($ruleGroup);

        $request = Request::create('/');
        $request->attributes->set('ruleGroupId', $uuid->toString());

        $argument = new ArgumentMetadata('ruleGroup', RuleGroup::class, false, false, null);

        self::assertSame(
            [$ruleGroup],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $ruleGroup = RuleGroup::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $request = Request::create('/');
        $request->attributes->set('ruleGroupId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('ruleGroup', RuleGroup::class, false, false, null);

        self::assertSame(
            [$ruleGroup],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveArchived(): void
    {
        $uuid = Uuid::v7();
        $ruleGroup = RuleGroup::fromArray(['id' => $uuid, 'status' => Status::Archived]);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroupArchive')
            ->willReturn($ruleGroup);

        $request = Request::create('/');
        $request->attributes->set('ruleGroupId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Archived->value);

        $argument = new ArgumentMetadata('ruleGroup', RuleGroup::class, false, false, null);

        self::assertSame(
            [$ruleGroup],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('ruleGroup', RuleGroup::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ruleGroupId', '42');

        $argument = new ArgumentMetadata('invalid', RuleGroup::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ruleGroupId', '42');

        $argument = new ArgumentMetadata('ruleGroup', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
