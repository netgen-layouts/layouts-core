<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\Route;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutResolverHandler::class)]
#[CoversClass(LayoutResolverQueryHandler::class)]
final class TargetHandlerTest extends TargetHandlerTestBase
{
    public function testMatchRulesWithNoTargetMatch(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published),
            $this->getTargetIdentifier(),
            'some_non_existent_route',
        );

        self::assertEmpty($rules);
    }

    public function testMatchRulesWithNonExistentTargetHandler(): void
    {
        $this->expectException(TargetHandlerException::class);
        $this->expectExceptionMessage('Doctrine target handler for "non_existent" target type does not exist.');

        $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published),
            'non_existent',
            'value',
        );
    }

    protected function getTargetIdentifier(): string
    {
        return 'route';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Route();
    }
}
