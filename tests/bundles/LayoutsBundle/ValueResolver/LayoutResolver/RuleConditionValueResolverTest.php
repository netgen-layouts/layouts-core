<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\RuleConditionValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleConditionValueResolver::class)]
final class RuleConditionValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleConditionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new RuleConditionValueResolver($this->layoutResolverServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $ruleCondition = RuleCondition::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->layoutResolverServiceStub
            ->method('loadRuleConditionDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleCondition);

        $request = Request::create('/');
        $request->attributes->set('conditionId', $uuid->toString());

        $argument = new ArgumentMetadata('condition', RuleCondition::class, false, false, null);

        self::assertSame(
            [$ruleCondition],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $ruleCondition = RuleCondition::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->layoutResolverServiceStub
            ->method('loadRuleCondition')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleCondition);

        $request = Request::create('/');
        $request->attributes->set('conditionId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('condition', RuleCondition::class, false, false, null);

        self::assertSame(
            [$ruleCondition],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('condition', RuleCondition::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('conditionId', '42');

        $argument = new ArgumentMetadata('invalid', RuleCondition::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('conditionId', '42');

        $argument = new ArgumentMetadata('condition', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
