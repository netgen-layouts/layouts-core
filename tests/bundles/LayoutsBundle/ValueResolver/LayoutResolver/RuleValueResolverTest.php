<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleValueResolver::class)]
final class RuleValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleValueResolver($this->layoutResolverServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $rule = Rule::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->layoutResolverServiceStub
            ->method('loadRuleDraft')
            ->willReturn($rule);

        $request = Request::create('/');
        $request->attributes->set('ruleId', $uuid->toString());

        $argument = new ArgumentMetadata('rule', Rule::class, false, false, null);

        self::assertSame(
            [$rule],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $rule = Rule::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->layoutResolverServiceStub
            ->method('loadRule')
            ->willReturn($rule);

        $request = Request::create('/');
        $request->attributes->set('ruleId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('rule', Rule::class, false, false, null);

        self::assertSame(
            [$rule],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveArchived(): void
    {
        $uuid = Uuid::v7();
        $rule = Rule::fromArray(['id' => $uuid, 'status' => Status::Archived]);

        $this->layoutResolverServiceStub
            ->method('loadRuleArchive')
            ->willReturn($rule);

        $request = Request::create('/');
        $request->attributes->set('ruleId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Archived->value);

        $argument = new ArgumentMetadata('rule', Rule::class, false, false, null);

        self::assertSame(
            [$rule],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('rule', Rule::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ruleId', '42');

        $argument = new ArgumentMetadata('invalid', Rule::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ruleId', '42');

        $argument = new ArgumentMetadata('rule', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
